<x-app-layout title="Businesses">
    <div class="space-y-6">
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-white/60 hover:text-white">← Back</a>
        <h2 class="text-2xl font-bold">Businesses</h2>
        <div class="glass-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="text-left p-4">Name</th>
                            <th class="text-left p-4">Category</th>
                            <th class="text-left p-4">Verified</th>
                            <th class="text-left p-4">Activated</th>
                            <th class="text-left p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($businesses as $business)
                            <tr class="border-t border-white/10">
                                <td class="p-4">{{ $business->name }}</td>
                                <td class="p-4">{{ $business->category }}</td>
                                <td class="p-4">{{ $business->verified ? '✓' : '—' }}</td>
                                <td class="p-4">{{ $business->activated ? '✓' : '—' }}</td>
                                <td class="p-4">
                                    @if(!$business->verified)
                                        <form method="POST" action="{{ route('admin.businesses.approve', $business) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-400 text-sm">Approve</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $businesses->links() }}
    </div>
</x-app-layout>
