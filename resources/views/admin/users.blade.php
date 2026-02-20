<x-app-layout title="Users">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Users</h2>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Name</th>
                            <th class="text-left p-4">Email/Phone</th>
                            <th class="text-left p-4">Redemptions</th>
                            <th class="text-left p-4">Joined</th>
                            <th class="text-left p-4">Status</th>
                            <th class="text-left p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $user->name }}</td>
                                <td class="p-4">{{ $user->email ?? $user->phone }}</td>
                                <td class="p-4">{{ $user->redemptions_count }}</td>
                                <td class="p-4">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="p-4">{{ $user->suspended ? 'Suspended' : 'Active' }}</td>
                                <td class="p-4">
                                    @if($user->suspended)
                                        <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-400 text-sm">Unsuspend</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="inline" onsubmit="return confirm('Suspend this user?')">
                                            @csrf
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
        {{ $users->links() }}
    </div>
</x-app-layout>
