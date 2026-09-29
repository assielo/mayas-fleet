<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use Illuminate\Http\Request;

class MissionController extends Controller
{
    public function index() { return response()->json(Mission::with(['vehicle', 'driver'])->get(), 200); }

    public function store(Request $request) {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'status' => 'sometimes|in:en_cours,livre,annule',
            'revenue' => 'sometimes|numeric',
        ]);
        return response()->json(['message' => 'Mission créée avec succès', 'data' => Mission::create($validated)], 201);
    }

    public function show(Mission $mission) { return response()->json($mission->load(['vehicle', 'driver']), 200); }

    public function update(Request $request, Mission $mission) {
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'driver_id' => 'sometimes|exists:drivers,id',
            'status' => 'sometimes|in:en_cours,livre,annule',
            'revenue' => 'sometimes|numeric',
        ]);
        $mission->update($validated);
        return response()->json(['message' => 'Mission mise à jour', 'data' => $mission], 200);
    }

    public function destroy(Mission $mission) {
        $mission->delete();
        return response()->json(['message' => 'Mission supprimée'], 200);
    }
}