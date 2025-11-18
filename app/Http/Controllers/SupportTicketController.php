<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = SupportTicket::with('user')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('priority'), fn($q) => $q->where('priority', $request->input('priority')))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('tickets.index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority']),
            'logTail' => $this->readLogs(),
            'systemInfo' => [
                'app' => config('app.name') . ' v' . config('app.version', '1.0.0'),
                'laravel' => app()->version(),
                'php' => PHP_VERSION,
            ],
        ]);
    }

    public function create(): View
    {
        return view('tickets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Support ticket submitted.');
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load('user');
        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,closed'],
            'priority' => ['required', 'in:low,normal,high'],
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated successfully.');
    }

    public function destroy(SupportTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket removed.');
    }

    protected function readLogs(): array
    {
        $path = storage_path('logs/laravel.log');
        if (! File::exists($path)) {
            return [];
        }

        $lines = collect(File::lines($path))->take(-50)->filter()->values();

        return $lines->all();
    }
}
