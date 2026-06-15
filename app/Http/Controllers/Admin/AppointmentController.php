<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/appointments/Index', [
            'appointments' => Appointment::with(['client', 'service'])
                ->orderBy('created_at', 'desc')
                ->get()
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $appointment->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('message', 'Estado del turno actualizado');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->back()->with('message', 'Turno eliminado correctamente');
    }
}
