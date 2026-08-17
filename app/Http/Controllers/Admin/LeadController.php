<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->when($request->input('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->input('source'), fn ($q, $source) => $q->where('source', $source))
            ->when($request->input('q'), function ($query, $term) {
                $like = '%'.$term.'%';

                $query->where(fn ($q) => $q
                    ->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('message', 'like', $like));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Contadores para las pestañas de estado, en una sola consulta.
        $counts = Lead::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.leads.index', [
            'leads' => $leads,
            'counts' => $counts,
            'total' => $counts->sum(),
        ]);
    }

    public function show(Lead $lead): View
    {
        $lead->load(['conversation', 'appointments']);

        return view('admin.leads.show', compact('lead'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $lead->update($data);

        return back()->with('success', 'Lead actualizado correctamente.');
    }
}
