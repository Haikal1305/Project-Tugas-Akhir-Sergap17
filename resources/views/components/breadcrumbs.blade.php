@unless ($breadcrumbs->isEmpty())
    <nav class="mb-4 text-sm text-slate-500 dark:text-slate-400 transition-colors duration-300" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2">
            @foreach ($breadcrumbs as $breadcrumb)
                @if (!$loop->first)
                    <li>
                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 dark:text-slate-600 transition-colors duration-300"></i>
                    </li>
                @endif

                <li>
                    @if ($breadcrumb->url && !$loop->last)
                        <a href="{{ $breadcrumb->url }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                            @if ($loop->first)
                                <i class="fa-solid fa-house"></i>
                            @else
                                {{ $breadcrumb->title }}
                            @endif
                        </a>
                    @else
                        <span class="font-medium text-slate-800 dark:text-slate-200 transition-colors duration-300">{{ $breadcrumb->title }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endunless