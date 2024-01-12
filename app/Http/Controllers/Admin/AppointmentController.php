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
}
