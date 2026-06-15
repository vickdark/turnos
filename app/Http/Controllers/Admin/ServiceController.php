<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render('admin/services/Index', [
            'services' => Service::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:sequential,scheduled',
            'duration_minutes' => 'required|integer|min:1',
            'capacity_per_slot' => 'required|integer|min:1',
        ]);

        Service::create($validated);

        return redirect()->back()->with('message', 'Servicio creado con éxito');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:sequential,scheduled',
            'duration_minutes' => 'required|integer|min:1',
            'capacity_per_slot' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $service->update($validated);

        return redirect()->back()->with('message', 'Servicio actualizado');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->back()->with('message', 'Servicio eliminado');
    }
}
