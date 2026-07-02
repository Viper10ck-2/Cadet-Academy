<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, AttendanceLocation, SchoolClass, Schedule, User};
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsenController extends Controller
{
    public function login(): View
    {
        return view('absen.login');
    }

    public function dashboard(): View
    {
        $user = auth()->user();
        $todayAtt = Attendance::where('user_id', $user->id)->whereDate('created_at', today())->get();
        $checkedIn = $todayAtt->where('type', 'check_in')->first();
        $checkedOut = $todayAtt->where('type', 'check_out')->first();
        $myClasses = $user->classes()->with('instructor')->get();

        // Check if there's an active schedule right now
        $todayName = strtolower(now()->locale('id')->dayName);
        $classIds = $myClasses->pluck('id');
        $activeSchedule = Schedule::whereIn('class_id', $classIds)
            ->where('day', $todayName)
            ->whereTime('start_time', '<=', now()->format('H:i:s'))
            ->whereTime('end_time', '>=', now()->format('H:i:s'))
            ->with('schoolClass')
            ->first();

        return view('absen.dashboard', compact('user', 'checkedIn', 'checkedOut', 'myClasses', 'activeSchedule'));
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        // 1. Validate schedule
        $todayName = strtolower(now()->locale('id')->dayName);
        $classIds = $user->classes()->pluck('classes.id');
        $activeSchedule = Schedule::whereIn('class_id', $classIds)
            ->where('day', $todayName)
            ->whereTime('start_time', '<=', now()->format('H:i:s'))
            ->whereTime('end_time', '>=', now()->format('H:i:s'))
            ->first();

        if (!$activeSchedule) {
            return response()->json(['error' => 'Maaf, Anda melewati waktu absensi.'], 400);
        }

        // 2. Validate check-in/check-out logic
        $type = $request->type ?? 'check_in';
        $todayAtt = Attendance::where('user_id', $user->id)->whereDate('created_at', today());
        $alreadyIn = (clone $todayAtt)->where('type', 'check_in')->exists();
        $alreadyOut = (clone $todayAtt)->where('type', 'check_out')->exists();

        if ($type === 'check_in' && $alreadyIn) {
            return response()->json(['error' => 'Anda sudah melakukan absensi masuk hari ini.'], 400);
        }
        if ($type === 'check_out' && !$alreadyIn) {
            return response()->json(['error' => 'Anda belum absen masuk hari ini.'], 400);
        }
        if ($type === 'check_out' && $alreadyOut) {
            return response()->json(['error' => 'Anda sudah melakukan absensi pulang hari ini.'], 400);
        }

        // 3. Validate GPS
        $activeLocation = AttendanceLocation::where('is_active', true)->first();
        if ($activeLocation) {
            $dist = $this->haversine(
                $activeLocation->latitude, $activeLocation->longitude,
                (float)$request->latitude, (float)$request->longitude
            );
            if ($dist > $activeLocation->radius_meters) {
                return response()->json(['error' => "Anda berada di luar area absensi. Jarak: " . round($dist) . "m (max: {$activeLocation->radius_meters}m)"], 400);
            }
        }

        // 4. Validate photo
        if (empty($request->photo)) {
            return response()->json(['error' => 'Foto selfie wajib diambil.'], 400);
        }

        // 5. Save photo
        $photoPath = null;
        if ($request->photo) {
            $img = preg_replace('/^data:image\/\w+;base64,/', '', $request->photo);
            $img = str_replace(' ', '+', $img);
            $photoPath = 'attendance/' . $user->id . '_' . now()->format('Ymd_His') . '.jpg';
            \Storage::disk('public')->put($photoPath, base64_decode($img));
        }

        // 6. Reverse geocode
        $alamat = $this->reverseGeocode((float)$request->latitude, (float)$request->longitude);

        // 7. Save attendance
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'type' => $request->type ?? 'check_in',
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath,
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $type === 'check_out' ? 'Absensi pulang berhasil! 👋' : 'Absensi masuk berhasil! ✅',
            'data' => [
                'waktu' => now()->format('H:i'),
                'status' => $type === 'check_out' ? 'Pulang' : 'Hadir',
                'lokasi' => $alamat,
                'foto' => asset('storage/' . $photoPath),
            ]
        ]);
    }

    public function history(): View
    {
        $attendances = Attendance::where('user_id', auth()->id())
            ->latest()->paginate(30)
            ->groupBy(fn($a) => $a->created_at->format('Y-m-d'));

        return view('absen.history', compact('attendances'));
    }

    public function profile(): View
    {
        return view('absen.profile', ['user' => auth()->user()]);
    }

    private function haversine($lat1, $lng1, $lat2, $lng2): float
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
        return $r * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    private function reverseGeocode($lat, $lng): string
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18";
            $ctx = stream_context_create(['http' => ['header' => 'User-Agent: CadetAcademy/1.0', 'timeout' => 3]]);
            $res = @file_get_contents($url, false, $ctx);
            if ($res) {
                $data = json_decode($res, true);
                return $data['display_name'] ?? "$lat, $lng";
            }
        } catch (\Exception $e) {}
        return "$lat, $lng";
    }
}
