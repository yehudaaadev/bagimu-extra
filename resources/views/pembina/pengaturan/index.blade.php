@extends('layouts.staff')

@section('title', 'Pengaturan — Pembina')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Pengaturan Pembina</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Kelola preferensi tema antarmuka dan tinjau informasi akun pembina ekstrakurikuler.
        </p>
    </div>

    {{-- Kartu Pilihan Tema Antarmuka --}}
    <x-theme-toggle-card />

    {{-- Kartu Pilihan Bahasa Antarmuka --}}
    <x-language-toggle-card />

    {{-- Informasi Akun Pembina --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Informasi Akun Pembina</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Data identitas pembina yang terdaftar aktif dalam sistem.</p>

        @php
            $user = auth()->user();
            $eskulBinaan = \App\Models\Eskul::where('pembina_id', $user->id)->get();
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-3.5">
                <span class="block text-slate-400 font-medium">Nama Pembina</span>
                <span class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5 block">{{ $user->name }}</span>
            </div>
            <div class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-3.5">
                <span class="block text-slate-400 font-medium">Alamat Surel</span>
                <span class="text-sm font-bold text-slate-800 dark:text-slate-100 mt-0.5 block">{{ $user->email }}</span>
            </div>
            <div class="rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/50 p-3.5 sm:col-span-2">
                <span class="block text-slate-400 font-medium">Ekstrakurikuler yang Diampu</span>
                @if ($eskulBinaan->isEmpty())
                    <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">Belum ada ekstrakurikuler yang ditugaskan ke akun ini.</span>
                @else
                    <div class="flex flex-wrap gap-2 mt-1.5">
                        @foreach ($eskulBinaan as $e)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 font-bold text-xs">
                                <x-icon name="sliders" class="h-3.5 w-3.5" />
                                {{ $e->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
