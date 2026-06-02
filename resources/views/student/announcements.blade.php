@extends('layouts.portal')
@section('title','Announcements – UM Clinic')
@section('page_title','Announcements')

@section('styles')
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
.afu { animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both; }

/* Announcement card */
.ann-card {
    background: #fff;
    border-radius: 1.15rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.ann-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.09); transform: translateY(-2px); }

/* Left accent bar color variants cycle */
.ann-accent-0 { border-left: 4px solid #991b1b; }
.ann-accent-1 { border-left: 4px solid #1d4ed8; }
.ann-accent-2 { border-left: 4px solid #059669; }
.ann-accent-3 { border-left: 4px solid #7c3aed; }
.ann-accent-4 { border-left: 4px solid #d97706; }

.ann-icon-0 { background:#fee2e2; color:#991b1b; }
.ann-icon-1 { background:#dbeafe; color:#1d4ed8; }
.ann-icon-2 { background:#d1fae5; color:#059669; }
.ann-icon-3 { background:#ede9fe; color:#7c3aed; }
.ann-icon-4 { background:#fef3c7; color:#d97706; }

/* Expand/collapse body */
.ann-body { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; transition: all .3s; }
.ann-body.expanded { display: block; -webkit-line-clamp: unset; }

.read-more-btn { font-size: .75rem; font-weight: 700; cursor: pointer; border: none; background: none; padding: 0; margin-top: .4rem; transition: color .15s; }
</style>
@endsection

@section('content')

{{-- ── HERO HEADER ── --}}
<div class="relative bg-gradient-to-br from-red-950 via-red-900 to-red-800 rounded-2xl overflow-hidden shadow-lg afu">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,.06),transparent_60%)] pointer-events-none"></div>
    <div class="absolute -bottom-8 -right-8 w-44 h-44 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="relative z-10 flex items-center justify-between px-6 py-5 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-white/15 rounded-xl flex items-center justify-center text-base">📢</div>
                <h2 class="text-white font-black text-lg">Announcements</h2>
            </div>
            <p class="text-red-300/75 text-xs">Latest health news &amp; updates from the UM Clinic</p>
        </div>
        <div class="text-center px-4 py-2 bg-white/10 rounded-xl border border-white/15 shrink-0">
            <div class="text-white font-black text-xl leading-none">{{ $announcements->count() }}</div>
            <div class="text-red-300/80 text-[.65rem] font-semibold uppercase tracking-wide mt-0.5">Posted</div>
        </div>
    </div>
</div>

{{-- ── ANNOUNCEMENTS LIST ── --}}
@forelse($announcements as $i => $ann)
@php $accent = $i % 5; @endphp
<div class="ann-card ann-accent-{{ $accent }} afu" style="animation-delay:{{ ($i % 8) * 55 }}ms">

    {{-- Card header --}}
    <div class="flex items-start gap-3 px-5 pt-4 pb-3">
        <div class="ann-icon-{{ $accent }} w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 font-bold">
            📢
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-black text-slate-800 text-sm leading-snug">{{ $ann->title }}</h3>
            <div class="flex items-center flex-wrap gap-2 mt-1">
                <span class="inline-flex items-center gap-1 text-[.67rem] text-slate-400 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $ann->created_at->format('F j, Y') }}
                </span>
                @if($ann->poster)
                <span class="text-[.67rem] text-slate-400">·</span>
                <span class="inline-flex items-center gap-1 text-[.67rem] text-slate-400 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    {{ $ann->poster->full_name }}
                </span>
                @endif
                <span class="text-[.67rem] font-semibold text-slate-400 ml-auto">
                    {{ $ann->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="px-5 pb-4 pl-[3.75rem]">
        <div class="ann-body text-slate-600 text-sm leading-relaxed" id="body-{{ $ann->id }}">{{ $ann->body }}</div>
        @if(strlen($ann->body) > 220)
        <button class="read-more-btn text-red-700 hover:text-red-900 mt-2"
                onclick="toggleBody({{ $ann->id }}, this)">
            Read more ↓
        </button>
        @endif
    </div>

</div>
@empty
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 py-20 text-center afu">
    <div class="w-16 h-16 bg-red-50 rounded-3xl flex items-center justify-center text-3xl mx-auto mb-4">📢</div>
    <h3 class="font-bold text-slate-700 text-base mb-1">No announcements yet</h3>
    <p class="text-slate-400 text-sm">Check back later for updates from the clinic.</p>
</div>
@endforelse

@endsection

@section('scripts')
<script>
function toggleBody(id, btn) {
    const body = document.getElementById('body-' + id);
    const expanded = body.classList.toggle('expanded');
    btn.textContent = expanded ? 'Show less ↑' : 'Read more ↓';
}
</script>
@endsection
