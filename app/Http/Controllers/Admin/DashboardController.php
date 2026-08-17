<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Conversation;
use App\Models\Lead;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'leadsTotal' => Lead::count(),
            'leadsNew' => Lead::where('status', 'new')->count(),
            'leadsThisMonth' => Lead::where('created_at', '>=', now()->startOfMonth())->count(),
            'leadsFromBot' => Lead::where('source', 'whatsapp')->count(),

            'conversationsTotal' => Conversation::count(),
            'conversationsWaiting' => Conversation::where('status', Conversation::STATUS_HUMAN)->count(),

            'appointmentsUpcoming' => Appointment::whereIn('status', Appointment::BLOCKING_STATUSES)
                ->where('scheduled_at', '>=', now())
                ->count(),

            'recentLeads' => Lead::latest()->limit(8)->get(),

            'nextAppointments' => Appointment::with('lead')
                ->whereIn('status', Appointment::BLOCKING_STATUSES)
                ->where('scheduled_at', '>=', now())
                ->orderBy('scheduled_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
