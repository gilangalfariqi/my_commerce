<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.users.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">User Profile</h1>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-4 mb-6 pb-4 border-b border-slate-100">
                <span class="w-16 h-16 rounded-2xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                    <div class="flex gap-2 mt-1.5">
                        @foreach($user->roles as $role)
                        @php
                            $roleColors = ['super-admin' => 'bg-purple-100 text-purple-700', 'admin' => 'bg-blue-100 text-blue-700', 'customer' => 'bg-slate-100 text-slate-600'];
                            $roleColor = $roleColors[$role->name] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $roleColor }}">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-slate-400">Phone</span><p class="font-medium text-slate-700 mt-0.5">{{ $user->phone ?? '—' }}</p></div>
                <div><span class="text-slate-400">Joined</span><p class="font-medium text-slate-700 mt-0.5">{{ $user->created_at->format('d M Y') }}</p></div>
                <div><span class="text-slate-400">Verified</span><p class="font-medium text-slate-700 mt-0.5">{{ $user->email_verified_at ? $user->email_verified_at->format('d M Y') : 'Not verified' }}</p></div>
                <div><span class="text-slate-400">Status</span><p class="font-medium mt-0.5 {{ $user->status === 'active' ? 'text-emerald-600' : 'text-rose-600' }}">{{ ucfirst($user->status ?? 'active') }}</p></div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="flex-1 flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors text-sm">
                <i class="fa-solid fa-user-pen"></i> Edit Roles
            </a>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50">Back</a>
        </div>
    </div>
</x-admin-layout>
