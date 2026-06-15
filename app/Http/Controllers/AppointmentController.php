<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\Service;
use App\Models\Appointment;
use Inertia\Inertia;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        return Inertia::render('booking/Index', [
            'services' => Service::where('is_active', true)->get()
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
            'first_name' => 'required_if:client_exists,false|string|max:255',
            'last_name' => 'required_if:client_exists,false|string|max:255',
            'service_id' => 'required|exists:services,id',
            'date' => 'required_if:type,scheduled|date|after_or_equal:today',
            'time' => 'required_if:type,scheduled|string',
        ]);

        $service = Service::find($request->service_id);

        // Buscar o crear cliente
        $client = Client::firstOrCreate(
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
            // Lógica para Turnos Secuenciales (Sala de espera)
            $lastSequence = Appointment::where('service_id', $service->id)
                ->whereDate('created_at', Carbon::today())
                ->max('sequence_number') ?? 0;
            
            $data['sequence_number'] = $lastSequence + 1;
        } else {
            // Lógica para Reservas (Calendario)
            $startTime = Carbon::parse($request->date . ' ' . $request->time);
            $data['start_time'] = $startTime;
            $data['end_time'] = (clone $startTime)->addMinutes($service->duration_minutes);
        }

        Appointment::create($data);

        return redirect()->route('home')->with('message', '¡Turno reservado y confirmado con éxito!');
    }
}
