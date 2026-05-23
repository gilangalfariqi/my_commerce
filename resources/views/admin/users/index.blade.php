<x-admin-layout>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Users</h1>
            <p class="text-sm text-slate-500 mt-1">Manage customer and admin accounts</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
        <div class="relative max-w-sm">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email…" class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary-400 bg-white">
        </div>
    </form>

    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Phone</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Joined</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm flex-shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-500 hidden sm:table-cell">{{ $user->phone ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @foreach($user->roles as $role)
                            @php
                                $roleColors = ['super-admin' => 'bg-purple-100 text-purple-700', 'admin' => 'bg-blue-100 text-blue-700', 'customer' => 'bg-slate-100 text-slate-600'];
                                $roleColor = $roleColors[$role->name] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleColor }}">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-400 hidden md:table-cell">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 rounded-lg text-slate-500 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                    <i class="fa-solid fa-user-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
                        <i class="fa-solid fa-users text-4xl mb-3 block"></i>
                        <p class="font-medium">No users found.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $users->withQueryString()->links() }}</div>
        @endif
    </div>
</x-admin-layout>
