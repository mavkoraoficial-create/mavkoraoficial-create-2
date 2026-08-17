<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $showPast = $request->boolean('past');

        $appointments = Appointment::query()
            ->with('lead')
            ->when(! $showPast, fn ($q) => $q->where('scheduled_at', '>=', now()->subHours(2)))
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->orderBy('scheduled_at', $showPast ? 'desc' : 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'showPast' => $showPast,
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Appointment::STATUSES))],
        ]);

        $appointment->update($data);

        return back()->with('success', 'Cita actualizada.');
    }
}
