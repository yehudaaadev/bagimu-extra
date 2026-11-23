<div x-data="{
    lang: localStorage.getItem('app_lang') || 'id',
    init() {
        window.addEventListener('language-changed', (e) => {
            this.lang = e.detail;
        });
    },
    setLanguage(val) {
        this.lang = val;
        localStorage.setItem('app_lang', val);
        if (typeof window.applyAppLanguage === 'function') {
            window.applyAppLanguage(val);
        }
        window.dispatchEvent(new CustomEvent('language-changed', { detail: val }));
    }
}" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs mb-6">
    <div class="mb-4">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white" data-i18n="Bahasa Antarmuka">Bahasa Antarmuka</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5" data-i18n="Pilih bahasa operasional sistem yang ingin digunakan.">Pilih bahasa operasional sistem yang ingin digunakan.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        {{-- Bahasa Indonesia --}}
        <button type="button" @click="setLanguage('id')"
            class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 transition-all cursor-pointer text-left focus:outline-hidden"
            :class="lang === 'id' ? 'border-blue-600 bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 shadow-2xs' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
            <div class="h-11 w-11 shrink-0 rounded-xl bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 flex items-center justify-center font-black text-xs">
                ID
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold block" data-i18n="Bahasa Indonesia (Bawaan)">Bahasa Indonesia (Bawaan)</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block" data-i18n="Bahasa resmi sistem dan instruksi sekolah">Bahasa resmi sistem dan instruksi sekolah</span>
            </div>
            <div x-show="lang === 'id'" class="h-2.5 w-2.5 rounded-full bg-blue-600 shrink-0"></div>
        </button>

        {{-- English --}}
        <button type="button" @click="setLanguage('en')"
            class="relative flex items-center gap-3.5 p-4 rounded-xl border-2 transition-all cursor-pointer text-left focus:outline-hidden"
            :class="lang === 'en' ? 'border-blue-600 bg-blue-50/60 dark:bg-blue-950/40 text-blue-900 dark:text-blue-200 shadow-2xs' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:bg-slate-50 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-300'">
            <div class="h-11 w-11 shrink-0 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 flex items-center justify-center font-black text-xs">
                EN
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-bold block">English (US/UK)</span>
                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5 block" data-i18n="Terjemahan bahasa Inggris menyeluruh">Terjemahan bahasa Inggris menyeluruh</span>
            </div>
            <div x-show="lang === 'en'" class="h-2.5 w-2.5 rounded-full bg-blue-600 shrink-0"></div>
        </button>
    </div>
</div>