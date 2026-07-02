<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LokasiAbsensiController extends Controller
{
    public function index(): View
    {
        $locations = AttendanceLocation::latest()->paginate(15);
        return view('admin.lokasi.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.lokasi.form', ['location' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
            'is_active' => 'boolean',
        ]);

        AttendanceLocation::create($validated);

        return redirect()->route('admin.lokasi.index')
            ->with('status', 'Lokasi absensi berhasil ditambahkan!');
    }

    public function edit(AttendanceLocation $lokasi): View
    {
        return view('admin.lokasi.form', ['location' => $lokasi]);
    }

    public function update(Request $request, AttendanceLocation $lokasi): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
            'is_active' => 'boolean',
        ]);

        $lokasi->update($validated);

        return redirect()->route('admin.lokasi.index')
            ->with('status', 'Lokasi absensi berhasil diperbarui!');
    }

    public function destroy(AttendanceLocation $lokasi): RedirectResponse
    {
        $lokasi->delete();
        return redirect()->route('admin.lokasi.index')
            ->with('status', 'Lokasi absensi berhasil dihapus!');
    }

    // Toggle active status
    public function toggle(AttendanceLocation $lokasi): RedirectResponse
    {
        $lokasi->update(['is_active' => !$lokasi->is_active]);
        return back()->with('status', 'Status lokasi diperbarui!');
    }
}
