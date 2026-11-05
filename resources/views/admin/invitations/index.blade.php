@extends('layouts.staff')

@section('title', 'Undang Pembina')

@section('content')
<h1 class="text-xl font-bold text-slate-900 mb-1">Undang Pembina Baru</h1>
<p class="text-sm text-slate-500 mb-4">Buat akun untuk pembina ekstrakurikuler atau pengelola sistem. Kredensial login (username & password) akan langsung ditampilkan dan dikirimkan ke email yang dituju.</p>

@if ($errors->any())
    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-semibold text-rose-800 space-y-1">
        @foreach ($errors->all() as $err)
            <div class="flex items-center gap-2">
                <x-icon name="alert-triangle" class="h-4 w-4 text-rose-600 shrink-0" />
                <span>{{ $err }}</span>
            </div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.invitations.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm w-full space-y-4" x-data="{ peran: '{{ old('role', 'PEMBINA') }}' }">
    @csrf
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap & Gelar</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd." required maxlength="100" class="w-full border-slate-300 rounded-xl text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email Gmail Resmi (untuk menerima undangan) <span class="text-rose-500">*</span></label>
        <div class="flex rounded-xl border border-slate-300 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-600/20 overflow-hidden bg-white shadow-2xs">
            <input type="text" name="email" value="{{ old('email') ? Str::before(old('email'), '@gmail.com') : '' }}" placeholder="cth: pembina.futsal" required class="w-full px-3.5 py-2 text-sm border-0 focus:outline-hidden">
            <span class="inline-flex items-center px-3 bg-slate-50 border-l border-slate-200 text-xs font-semibold text-slate-500 select-none">@gmail.com</span>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Peran / Penugasan</label>
        <select name="role" x-model="peran" class="w-full border-slate-300 rounded-xl text-sm">
            <option value="PEMBINA">Pembina Ekstrakurikuler</option>
            <option value="OPERATOR">Operator Sistem</option>
            <option value="ADMIN">Administrator Penuh</option>
        </select>
    </div>
    <div x-show="peran === 'PEMBINA'">
        <span class="block text-sm font-medium text-slate-700 mb-1">Eskul yang Diampu <span class="font-normal text-slate-400">(1 eskul = 1 akun bersama)</span></span>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-3">
            @forelse ($eskuls as $e)
                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm {{ $e->pembina_id ? 'opacity-60 cursor-not-allowed' : 'hover:bg-white cursor-pointer' }}">
                    <input type="checkbox" name="eskul_ids[]" value="{{ $e->id }}" @checked(in_array($e->id, old('eskul_ids', []))) {{ $e->pembina_id ? 'disabled' : '' }} class="rounded border-slate-300 text-blue-700 focus:ring-blue-500">
                    <span>{{ $e->name }}</span>
                    @if ($e->pembina_id)
                        <span class="text-[10px] text-amber-700 font-medium">(sudah memiliki akun pembina)</span>
                    @endif
                </label>
            @empty
                <p class="text-xs text-slate-400">Belum ada eskul. Buat dulu di Master Eskul.</p>
            @endforelse
        </div>
    </div>
    <button class="rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition shadow-sm">Buat & Undang Pembina</button>
</form>
@endsection
