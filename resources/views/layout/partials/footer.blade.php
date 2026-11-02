{{-- Komponen: Footer --}}
<footer class="px-4 sm:px-6 lg:px-8 py-6 mt-auto border-t border-slate-200/60 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm transition-colors duration-300">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">

        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400 font-medium">
            <p>&copy; {{ date('Y') }} <span class="font-bold text-slate-800 dark:text-white">PT SERGAP 17</span>. All Systems Operational.</p>
        </div>

        <div class="flex items-center gap-5 sm:gap-6 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider">
            <a href="#" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Tentang</a>
            <a href="#" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Privasi</a>
            <a href="#" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">Kontak IT</a>
        </div>

    </div>
</footer>