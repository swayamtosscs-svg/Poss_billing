<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            {{ __('Support & System Status') }}
        </h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-200 shadow rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase">{{ __('System Info') }}</h3>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li>{{ __('Application') }}: {{ $systemInfo['app'] }}</li>
                    <li>{{ __('Laravel') }}: {{ $systemInfo['laravel'] }}</li>
                    <li>{{ __('PHP Version') }}: {{ $systemInfo['php'] }}</li>
                </ul>
            </div>
            <div class="bg-white border border-gray-200 shadow rounded-lg p-6 md:col-span-2">
                <h3 class="text-sm font-semibold text-gray-500 uppercase">{{ __('Recent Logs') }}</h3>
                <div class="mt-3 bg-gray-100 text-gray-800 rounded p-3 h-48 overflow-y-auto text-xs font-mono border border-gray-200">
                    @forelse($logTail as $line)
                        <div>{{ $line }}</div>
                    @empty
                        <div>{{ __('No logs recorded yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <form method="GET" class="flex items-center gap-4">
                    <select name="status"
                            class="rounded-md border-gray-300 bg-white text-gray-900">
                        <option value="">{{ __('All Status') }}</option>
                        @foreach(['open','in_progress','closed'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                        @endforeach
                    </select>
                    <select name="priority"
                            class="rounded-md border-gray-300 bg-white text-gray-900">
                        <option value="">{{ __('All Priorities') }}</option>
                        @foreach(['low','normal','high'] as $priority)
                            <option value="{{ $priority }}" @selected(($filters['priority'] ?? null) === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Filter') }}
                    </button>
                </form>
                <a href="{{ route('tickets.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md shadow hover:bg-emerald-500">
                    {{ __('New Support Ticket') }}
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 bg-white">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Ticket') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Priority') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <div class="font-semibold">#{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} - {{ $ticket->subject }}</div>
                                <div class="text-xs text-gray-500">{{ $ticket->user?->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @class([
                                        'bg-gray-200 text-gray-800' => $ticket->priority === 'low',
                                        'bg-amber-200 text-amber-800' => $ticket->priority === 'normal',
                                        'bg-red-200 text-red-800' => $ticket->priority === 'high',
                                    ])">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @class([
                                        'bg-blue-200 text-blue-800' => $ticket->status === 'open',
                                        'bg-amber-200 text-amber-800' => $ticket->status === 'in_progress',
                                        'bg-emerald-200 text-emerald-800' => $ticket->status === 'closed',
                                    ])">
                                    {{ ucfirst(str_replace('_',' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $ticket->created_at?->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-500">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('No support tickets found.') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-gray-100">
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

