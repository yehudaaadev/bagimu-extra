{{-- Dark Mode Universal Engine (Instant & Reactive) --}}
<style>
    html.dark {
        color-scheme: dark;
    }

    /* 1. Canvas & Dasar Halaman */
    html.dark body {
        background-color: #090e17 !important;
        color: #f1f5f9 !important;
    }

    html.dark .min-h-screen,
    html.dark .bg-slate-50,
    html.dark .bg-slate-100\/90,
    html.dark .bg-slate-100\/70 {
        background-color: #090e17 !important;
    }

    /* 2. Sidebar, Header & Wadah Kartu */
    html.dark aside,
    html.dark header,
    html.dark .bg-white,
    html.dark .bg-white\/95,
    html.dark .bg-white\/90,
    html.dark .bg-white\/80 {
        background-color: #111a2e !important;
    }

    /* 3. Garis Batas & Pembatas */
    html.dark .border-slate-100,
    html.dark .border-slate-100\/90,
    html.dark .border-slate-200,
    html.dark .border-slate-200\/80,
    html.dark .border-slate-200\/70,
    html.dark .border-slate-200\/90,
    html.dark .border-slate-300,
    html.dark hr {
        border-color: #1e293b !important;
    }

    html.dark .divide-slate-100 > :not([hidden]) ~ :not([hidden]),
    html.dark .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
        border-color: #1e293b !important;
    }

    /* 4. Tipografi Teks */
    html.dark .text-slate-900,
    html.dark .text-slate-800 {
        color: #f8fafc !important;
    }

    html.dark .text-slate-700,
    html.dark .text-slate-600 {
        color: #cbd5e1 !important;
    }

    html.dark .text-slate-500 {
        color: #94a3b8 !important;
    }

    html.dark .text-slate-400 {
        color: #64748b !important;
    }

    /* 5. Tabel Data */
    html.dark thead,
    html.dark thead tr,
    html.dark thead th {
        background-color: #0c1424 !important;
        color: #cbd5e1 !important;
        border-color: #1e293b !important;
    }

    html.dark tbody tr:hover,
    html.dark .hover\:bg-slate-50:hover,
    html.dark .hover\:bg-slate-50\/80:hover,
    html.dark .hover\:bg-slate-50\/60:hover,
    html.dark .hover\:bg-slate-100:hover,
    html.dark .hover\:bg-slate-100\/90:hover {
        background-color: #162238 !important;
    }

    /* 6. Kontrol Formulir & Input */
    html.dark input[type="text"],
    html.dark input[type="search"],
    html.dark input[type="password"],
    html.dark input[type="email"],
    html.dark input[type="number"],
    html.dark input[type="date"],
    html.dark input[type="time"],
    html.dark input[type="datetime-local"],
    html.dark select,
    html.dark textarea {
        background-color: #0b1120 !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }

    html.dark select option {
        background-color: #0f172a !important;
        color: #f8fafc !important;
    }

    html.dark input::placeholder,
    html.dark textarea::placeholder {
        color: #64748b !important;
    }

    /* 7. Tombol Putih & Abu */
    html.dark button.bg-white,
    html.dark a.bg-white {
        background-color: #131f38 !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    html.dark button.bg-white:hover,
    html.dark a.bg-white:hover {
        background-color: #1e2f50 !important;
        color: #ffffff !important;
    }

    /* 8. Badge & Elemen Sekunder */
    html.dark .bg-slate-100,
    html.dark .bg-slate-200 {
        background-color: #1a253a !important;
        color: #cbd5e1 !important;
        border-color: #1e293b !important;
    }

    /* 9. Dropdown & Panel Mengambang */
    html.dark [role="dialog"],
    html.dark .shadow-xs,
    html.dark .shadow-sm,
    html.dark .shadow-md,
    html.dark .shadow-lg,
    html.dark .shadow-xl {
        box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.55), 0 4px 8px -2px rgba(0, 0, 0, 0.35) !important;
    }

    /* 10. Scrollbar Mode Gelap */
    html.dark ::-webkit-scrollbar-thumb {
        background: #334155 !important;
    }
    html.dark ::-webkit-scrollbar-thumb:hover {
        background: #475569 !important;
    }

    /* 11. Reset Border Input Gabungan (Addon Group) */
    .border-0,
    input.border-0 {
        border-width: 0 !important;
        border-style: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
</style>
