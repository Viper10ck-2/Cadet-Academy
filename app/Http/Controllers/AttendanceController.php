<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    // Attendance page with camera & GPS
    public function index(): View
    {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->get();

        $hasCheckedIn = $todayAttendance->where('type', 'check_in')->count() > 0;
        $hasCheckedOut = $todayAttendance->where('type', 'check_out')->count() > 0;
        $locations = AttendanceLocation::where('is_active', true)->get();

        $history = Attendance::where('user_id', $user->id)
            ->latest()
            ->take(30)
            ->get()
            ->groupBy(fn ($item) => $item->created_at->format('Y-m-d'));

        return view('attendance.index', compact(
            'todayAttendance',
            'hasCheckedIn',
            'hasCheckedOut',
            'locations',
            'history'
        ));
    }

    // Store attendance (check-in / check-out)
    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'type' => 'required|in:check_in,check_out',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|string', // base64 image
        ]);

        $user = auth()->user();

        // Check duplicate
        $alreadyExists = Attendance::where('user_id', $user->id)
            ->where('type', $request->type)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyExists) {
            return back()->with('error', 'Anda sudah melakukan ' . ($request->type === 'check_in' ? 'check-in' : 'check-out') . ' hari ini.');
        }

        // Validate location
        $activeLocation = AttendanceLocation::where('is_active', true)->first();
        if ($activeLocation && !$activeLocation->isWithinRadius((float) $request->latitude, (float) $request->longitude)) {
            return back()->with('error', 'Anda berada di luar area absensi yang diizinkan.');
        }

        // Save photo
        $photoPath = null;
        if ($request->photo) {
            $image = str_replace('data:image/jpeg;base64,', '', $request->photo);
            $image = str_replace('data:image/png;base64,', '', $request->photo);
            $image = str_replace(' ', '+', $image);
            $imageName = 'attendance/' . $user->id . '_' . $request->type . '_' . now()->format('Ymd_His') . '.jpg';
            \Storage::disk('public')->put($imageName, base64_decode($image));
            $photoPath = $imageName;
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath,
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($request->type === 'check_in' ? 'Check-in' : 'Check-out') . ' berhasil!',
                'attendance' => $attendance,
            ]);
        }

        return back()->with('status', ucfirst($request->type === 'check_in' ? 'Check-in' : 'Check-out') . ' berhasil!');
    }
}
