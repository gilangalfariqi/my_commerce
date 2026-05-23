<x-admin-layout>
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('admin.users.show', $user->id) }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h1 class="text-2xl font-bold text-slate-900">Edit User Roles: {{ $user->name }}</h1>
    </div>

    <div class="max-w-lg">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                    <span class="w-10 h-10 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    <div>
                        <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                    </div>
                </div>

                <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Assign Roles *</h3>
                <div class="space-y-3">
                    @foreach($roles as $role)
                    @php
                        $roleColors = ['super-admin' => 'border-purple-200 bg-purple-50', 'admin' => 'border-blue-200 bg-blue-50', 'customer' => 'border-slate-200 bg-slate-50'];
                        $roleColor = $roleColors[$role->name] ?? 'border-slate-200 bg-slate-50';
                    @endphp
                    <label class="flex items-start gap-3 p-4 rounded-xl border {{ $roleColor }} cursor-pointer hover:opacity-90 transition-opacity">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                            class="mt-0.5 rounded border-slate-300 text-primary-600 focus:ring-primary-400">
                        <div>
                            <p class="font-semibold text-slate-800 capitalize">{{ $role->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                @if($role->name === 'super-admin') Full access to all admin features
                                @elseif($role->name === 'admin') Manage products, orders, and content
                                @else Regular customer account
                                @endif
                            </p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('roles') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 rounded-xl transition-colors text-sm">
                    <i class="fa-solid fa-floppy-disk mr-2"></i> Save Roles
                </button>
                <a href="{{ route('admin.users.show', $user->id) }}" class="px-6 py-3 border border-slate-200 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>
</x-admin-layout>
