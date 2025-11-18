<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Ticket #:number', ['number' => str_pad($ticket->id, 4, '0', STR_PAD_LEFT)]) }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at?->format('d M Y H:i') }}</p>
            </div>
            <a href="{{ route('tickets.index') }}" class="text-sm text-blue-600 hover:text-blue-500">{{ __('Back to list') }}</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $ticket->subject }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Requested by') }} {{ $ticket->user?->name ?? __('System') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @class([
                            'bg-gray-200 text-gray-800' => $ticket->priority === 'low',
                            'bg-amber-200 text-amber-800' => $ticket->priority === 'normal',
                            'bg-red-200 text-red-800' => $ticket->priority === 'high',
                        ])">
                        {{ ucfirst($ticket->priority) }} {{ __('priority') }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @class([
                            'bg-blue-200 text-blue-800' => $ticket->status === 'open',
                            'bg-amber-200 text-amber-800' => $ticket->status === 'in_progress',
                            'bg-emerald-200 text-emerald-800' => $ticket->status === 'closed',
                        ])">
                        {{ ucfirst(str_replace('_',' ', $ticket->status)) }}
                    </span>
                </div>
            </div>
            <div class="text-gray-700 dark:text-gray-200 whitespace-pre-line">
                {{ $ticket->message }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-6">
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Status') }}</label>
                    <select name="status"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        @foreach(['open','in_progress','closed'] as $status)
                            <option value="{{ $status }}" @selected($ticket->status === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('Priority') }}</label>
                    <select name="priority"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                        @foreach(['low','normal','high'] as $priority)
                            <option value="{{ $priority }}" @selected($ticket->priority === $priority)>{{ ucfirst($priority) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                </div>
                <div class="flex items-end justify-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-500">
                        {{ __('Update Ticket') }}
                    </button>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('{{ __('Close and delete this ticket?') }}');"
              class="text-right">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-500">
                {{ __('Delete Ticket') }}
            </button>
        </form>
    </div>
</x-app-layout>

