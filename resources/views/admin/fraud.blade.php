<x-app-layout title="Fraud Flags">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Fraud Flags</h2>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">User</th>
                            <th class="text-left p-4">Reason</th>
                            <th class="text-left p-4">Status</th>
                            <th class="text-left p-4">Date</th>
                            <th class="text-left p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flags as $flag)
                            <tr class="border-t border-white/10">
                                <td class="p-4">
                                    @if($flag->user)
                                        {{ $flag->user->name }} ({{ $flag->user->email ?? $flag->user->phone }})
                                        @if($flag->user->suspended)
                                            <span class="text-red-400 text-xs">Suspended</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="p-4">{{ $flag->reason }}</td>
                                <td class="p-4">{{ ucfirst($flag->status) }}</td>
                                <td class="p-4">{{ $flag->created_at->format('M d, H:i') }}</td>
                                <td class="p-4">
                                    @if($flag->status === 'pending')
                                        <form method="POST" action="{{ route('admin.fraud.review', $flag) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="clear">
                                            <button type="submit" class="text-emerald-400 text-sm mr-2">Clear</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.fraud.review', $flag) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="suspend">
                                            <button type="submit" class="text-red-400 text-sm">Suspend</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $flags->links() }}
    </div>
</x-app-layout>
