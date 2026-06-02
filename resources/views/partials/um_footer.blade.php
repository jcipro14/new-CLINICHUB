<footer class="w-full" style="position:relative;z-index:10;">

    {{-- ── MAIN FOOTER ── --}}
    <div style="background:#991b1b;">
        <div class="max-w-5xl mx-auto px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center sm:items-stretch gap-4">

                {{-- Left: Brand --}}
                <div class="flex items-center gap-3 flex-1">
                    <img src="{{ asset('images/um_logo_no_bg.png') }}"
                         alt="UM Logo" class="w-9 h-9 object-contain drop-shadow shrink-0">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-white font-black text-lg leading-none">UM</span>
                            <span class="text-white/80 font-semibold text-xs">· The University of Mindanao</span>
                        </div>
                        <p class="text-white/55 text-[.68rem] mt-0.5">Davao City 8000, Philippines &nbsp;|&nbsp; +63 (082) 221 0190 &nbsp;|&nbsp; +63 (082) 305 0645</p>
                    </div>
                </div>

                {{-- Gold vertical divider --}}
                <div class="hidden sm:block self-stretch" style="width:1px;background:rgba(217,163,6,.5);flex-shrink:0;margin:0 .75rem;"></div>

                {{-- Right: Branches --}}
                <div class="flex items-center gap-3 flex-wrap justify-center sm:justify-start">
                    <span class="text-white/50 text-[.65rem] font-black uppercase tracking-widest shrink-0">Branches</span>
                    @foreach(['TAGUM','PANABO','DIGOS','BANSALAN','PEÑALATA'] as $_branch)
                    <span class="text-white/75 text-[.7rem] font-semibold">{{ $_branch }}</span>
                    @if(!$loop->last)<span class="text-white/25 text-xs">·</span>@endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- ── GOLD BOTTOM BAR ── --}}
    <div style="background:#d4940a;" class="px-6 py-1.5 flex flex-wrap justify-between items-center gap-2">
        <span class="text-[.68rem] font-bold" style="color:rgba(0,0,0,.7);">
            Copyright &copy; {{ date('Y') }}, All Rights Reserved.
        </span>
        <span class="text-[.68rem] font-bold" style="color:rgba(0,0,0,.7);">
            UM STUDENT PORTAL TAGUM
        </span>
    </div>

</footer>
