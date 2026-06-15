<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use App\Models\Appointment;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('booking/Index', [
            'services' => Service::where('is_active', true)->get()
        ]);
    }

    public function monitor()
    {
        // Obtenemos los turnos de hoy que no han sido completados ni cancelados
        $appointments = Appointment::with(['client', 'service'])
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('booking/Monitor', [
            'appointments' => $appointments
        ]);
    }

    public function checkClient(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        
        $client = Client::where('phone', $request->phone)->first();
        
        return response()->json([
            'exists' => !!$client,
            'client' => $client
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
        ]);

        $service = Service::find($request->service_id);

        // Buscar o crear cliente siempre
        $client = Client::updateOrCreate(
            ['phone' => $request->phone],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name
            ]
        );

        $data = [
            'client_id' => $client->id,
            'service_id' => $service->id,
            'status' => 'confirmed',
            'notes' => $request->notes,
        ];

        if ($service->type === 'sequential') {
            $lastSequence = Appointment::where('service_id', $service->id)
                ->whereDate('created_at', Carbon::today())
                ->max('sequence_number') ?? 0;
            
            $data['sequence_number'] = $lastSequence + 1;
        } else {
            $startTime = Carbon::parse($request->date . ' ' . $request->time);
            $data['start_time'] = $startTime;
            $data['end_time'] = (clone $startTime)->addMinutes($service->duration_minutes);
        }

        $appointment = Appointment::create($data);

        return Inertia::render('booking/Success', [
            'appointment' => $appointment->load(['client', 'service'])
        ]);
    }
}
