<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::query()
            ->with('client:id,first_name,last_name')
            ->when(request('status'), function ($query) {
                $query->where('status', AppointmentStatus::from(request('status')));
            })
            ->latest()
            ->paginate(10)
            ->through(fn($appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('Y-m-d h:i:s'),
                'end_time' => $appointment->end_time->format('Y-m-d h:i:s'),
                'client' => $appointment->client,
                'status' => [
                    'name' => $appointment->status->name,
                    'color' => $appointment->status->color(),
                ],
            ]);
    }
    public function store()
    {
        $validated = request()->validate([
            'title' => 'required',
            'client_id' => 'required',
            'description' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ], [
            'client_id.required' => 'the client name field is required'
        ]);
        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description' => $validated['description'],
            'status' => AppointmentStatus::SCHEDULED,
        ]);
        return response()->json(['message' => 'Appointment created']);
    }
    public function edit($id)
    {
        $appointment = Appointment::find($id);
        return $appointment;
    }
    public function update($id)
    {
        $appointment = Appointment::find($id);

        $validated = request()->validate([
            'title' => 'required',
            'client_id' => 'required',
            'description' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ], [
            'client_id.required' => 'the client name field is required'
        ]);
        $appointment->update([
            'title' => $validated['title'],
            'client_id' => $validated['client_id'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'description' => $validated['description'],
            'status' => $appointment->status,
        ]);
        return response()->json(['message' => 'Appointment updated successfully']);
    }

    public function delete($id)
    {
        Appointment::find($id)->delete();
        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}
