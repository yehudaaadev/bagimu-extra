<div x-data="{
    theme: localStorage.getItem('theme') || 'light',
    init() {
        window.addEventListener('theme-changed', (e) => {
            this.theme = e.detail;
        });
    },
    setTheme(val) {
        this.theme = val;
        localStorage.setItem('theme', val);
        if (val === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: val }));
    }
}" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs mb-6">
    <div class="mb-4">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tema Antarmuka</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pilih tampilan antarmuka yang nyaman untuk visual dan kenyamanan kerja Anda.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        {{-- Mode Terang --}}
        <button type="button" @click="setTheme('light')"
            class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 transition-all cursor-pointer text-left focus:outline-hidden"
            :class="theme === 'light' ? 'border-blue-600 bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 shadow-2xs' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
            <div class="h-11 w-11 shrink-0 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                <x-icon name="sun" class="h-5 w-5" />
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold block">Mode Terang (Bawaan)</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block">Tampilan bersih cerah untuk siang hari</span>
            </div>
            <div x-show="theme === 'light'" class="h-2.5 w-2.5 rounded-full bg-blue-600 shrink-0"></div>
        </button>

        {{-- Mode Gelap --}}
        <button type="button" @click="setTheme('dark')"
            class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 transition-all cursor-pointer text-left focus:outline-hidden"
            :class="theme === 'dark' ? 'border-blue-600 bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 shadow-2xs' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
            <div class="h-11 w-11 shrink-0 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                <x-icon name="moon" class="h-5 w-5" />
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold block">Mode Gelap</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block">Tampilan redup nyaman untuk malam hari</span>
            </div>
            <div x-show="theme === 'dark'" class="h-2.5 w-2.5 rounded-full bg-blue-600 shrink-0"></div>
        </button>
    </div>
</div>