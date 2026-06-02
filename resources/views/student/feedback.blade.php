@extends('layouts.portal')
@section('title','Feedback – UM Clinic')
@section('page_title','Feedback')

@section('styles')
<style>
/* ── ANIMATIONS ─────────────────────────────────── */
@keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
.afu  { animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both; }
.d1{animation-delay:.05s} .d2{animation-delay:.10s} .d3{animation-delay:.16s}

/* ── SCROLL REVEAL ─────────────────────────────── */
.reveal { opacity:0; transform:translateY(18px); transition:opacity .5s ease,transform .5s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }

/* ── STAR RATING ────────────────────────────────── */
.star-group { display:flex; flex-direction:row-reverse; justify-content:flex-end; gap:.35rem; }
.star-group input { display:none; }
.star-group label {
    font-size: 2rem; cursor: pointer; color: #e2e8f0;
    transition: color .15s, transform .15s;
    line-height: 1;
}
.star-group label:hover,
.star-group label:hover ~ label,
.star-group input:checked ~ label { color: #f59e0b; }
.star-group label:hover { transform: scale(1.15); }
.star-group input:checked + label { transform: scale(1.1); }

/* ── TEXTAREA ────────────────────────────────────── */
.fb-textarea {
    width: 100%; border: 1.5px solid #e2e8f0; border-radius: .85rem;
    padding: .85rem 1rem; font-size: .875rem; color: #1e293b;
    resize: vertical; min-height: 120px; outline: none;
    transition: border-color .2s, box-shadow .2s;
    font-family: inherit; line-height: 1.6;
}
.fb-textarea:focus { border-color: #991b1b; box-shadow: 0 0 0 3px rgba(153,27,27,.1); }
.fb-textarea::placeholder { color: #94a3b8; }

/* ── CHAR COUNTER ────────────────────────────────── */
.char-count { font-size: .68rem; font-weight: 600; transition: color .2s; }

/* ── SUBMIT BUTTON ───────────────────────────────── */
.fb-btn {
    width: 100%; background: linear-gradient(135deg,#7f1d1d,#991b1b);
    color: #fff; font-weight: 800; font-size: .9rem; padding: .8rem;
    border-radius: .9rem; border: none; cursor: pointer;
    transition: transform .15s, box-shadow .2s, background .2s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    box-shadow: 0 4px 16px rgba(153,27,27,.3);
}
.fb-btn:hover  { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(153,27,27,.35); background:linear-gradient(135deg,#6b1313,#7f1d1d); }
.fb-btn:active { transform: scale(.99); }
.fb-btn:disabled { opacity:.6; cursor:not-allowed; transform:none; }

/* ── FEEDBACK HISTORY CARD ───────────────────────── */
.fb-card {
    background: #fff; border-radius: 1.1rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.fb-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.08); transform: translateY(-2px); }

/* ── STAR DISPLAY ────────────────────────────────── */
.star-display { display:inline-flex; gap:.15rem; }
.star-on  { color:#f59e0b; }
.star-off { color:#e2e8f0; }
</style>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

{{-- ── HERO HEADER ── --}}
<div class="relative bg-gradient-to-br from-red-950 via-red-900 to-red-800 rounded-2xl overflow-hidden shadow-lg afu">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,.07),transparent_60%)] pointer-events-none"></div>
    <div class="absolute -bottom-6 -right-6 w-36 h-36 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="relative z-10 px-6 py-6 flex items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-8 h-8 bg-white/15 rounded-xl flex items-center justify-center text-lg">💬</div>
                <h2 class="text-white font-black text-lg">Share Your Feedback</h2>
            </div>
            <p class="text-red-300/75 text-xs leading-relaxed max-w-sm">
                Your honest feedback helps the UM Clinic improve its services for all students.
            </p>
        </div>
        @if($feedbacks->isNotEmpty())
        <div class="text-center bg-white/10 border border-white/15 rounded-xl px-4 py-2.5 shrink-0">
            <div class="text-white font-black text-xl leading-none">{{ $feedbacks->count() }}</div>
            <div class="text-red-300/75 text-[.62rem] font-semibold mt-0.5 uppercase tracking-wide">Submitted</div>
        </div>
        @endif
    </div>
</div>

{{-- ── SUCCESS / ERROR ALERTS ── --}}
@if(session('success'))
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl text-sm font-semibold shadow-sm afu">
    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3.5 rounded-2xl text-sm font-semibold">
    @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
</div>
@endif

{{-- ── FEEDBACK FORM ── --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden afu d1">

    {{-- Card header --}}
    <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-slate-50/50">
        <div class="w-8 h-8 bg-red-100 rounded-xl flex items-center justify-center text-base">📝</div>
        <div>
            <h3 class="font-black text-slate-800 text-sm">New Feedback</h3>
            <p class="text-[.68rem] text-slate-400">All submissions are anonymous to other students</p>
        </div>
    </div>

    <form method="POST" action="{{ route('student.feedback.save') }}" class="p-6 space-y-5" id="fbForm">
        @csrf

        {{-- ── STAR RATING ── --}}
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                Rate Your Experience
                <span class="text-slate-300 font-normal normal-case tracking-normal ml-1">(optional)</span>
            </label>

            {{-- Visual star picker --}}
            <div class="star-group mb-3" id="starPicker">
                @foreach(range(5,1) as $r)
                <input type="radio" name="rating" id="star{{ $r }}" value="{{ $r }}">
                <label for="star{{ $r }}" title="{{ $r }} star{{ $r > 1 ? 's' : '' }}">★</label>
                @endforeach
            </div>
            <p id="ratingLabel" class="text-xs text-slate-400 font-medium h-4"></p>
        </div>

        {{-- ── MESSAGE ── --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Message <span class="text-red-500">*</span>
                </label>
                <span id="charCount" class="char-count text-slate-300">0 / 1000</span>
            </div>
            <textarea name="message" id="fbMessage" class="fb-textarea" rows="5"
                      placeholder="Share your experience with the clinic — your thoughts on the service, staff, or any suggestions for improvement..."
                      required maxlength="1000"
                      oninput="updateChar(this)"></textarea>
        </div>

        {{-- ── SUBMIT ── --}}
        <button type="submit" class="fb-btn" id="fbSubmit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            Submit Feedback
        </button>
    </form>
</div>

{{-- ── PREVIOUS FEEDBACK ── --}}
@if($feedbacks->isNotEmpty())
<div class="reveal">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
            <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center text-sm">⭐</span>
            Your Previous Feedback
        </h3>
        <span class="text-[.68rem] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">
            {{ $feedbacks->count() }} total
        </span>
    </div>

    <div class="space-y-3">
        @foreach($feedbacks as $i => $fb)
        <div class="fb-card" style="animation:fadeUp .4s cubic-bezier(.4,0,.2,1) {{ $i * 55 }}ms both">

            {{-- Card top --}}
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-50">

                {{-- Stars --}}
                <div class="flex items-center gap-2">
                    @if($fb->rating)
                    <div class="star-display">
                        @for($s = 1; $s <= 5; $s++)
                        <span class="{{ $s <= $fb->rating ? 'star-on' : 'star-off' }}" style="font-size:1rem;">★</span>
                        @endfor
                    </div>
                    <span class="text-[.7rem] font-bold text-amber-600">{{ $fb->rating }}/5</span>
                    @else
                    <span class="text-[.7rem] text-slate-400 italic">No rating</span>
                    @endif
                </div>

                {{-- Date --}}
                <div class="flex items-center gap-1.5 text-[.68rem] text-slate-400 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($fb->created_at)->format('M d, Y') }}
                    <span class="text-slate-300">·</span>
                    {{ \Carbon\Carbon::parse($fb->created_at)->diffForHumans() }}
                </div>
            </div>

            {{-- Message --}}
            <div class="px-5 py-4">
                <p class="text-slate-700 text-sm leading-relaxed">{{ $fb->message }}</p>
            </div>

        </div>
        @endforeach
    </div>
</div>
@else
{{-- Empty state for previous feedback --}}
<div class="bg-white rounded-2xl border border-dashed border-slate-200 py-10 text-center reveal">
    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-3">💬</div>
    <p class="font-bold text-slate-600 text-sm">No feedback submitted yet</p>
    <p class="text-slate-400 text-xs mt-1">Your submitted feedback will appear here</p>
</div>
@endif

</div>{{-- /.max-w-2xl --}}
@endsection

@section('scripts')
<script>
/* ── STAR RATING LABEL ────────────────────────────── */
const ratingLabels = { 1:'Poor', 2:'Fair', 3:'Good', 4:'Very Good', 5:'Excellent!' };
const ratingColors = { 1:'#ef4444', 2:'#f97316', 3:'#eab308', 4:'#22c55e', 5:'#16a34a' };

document.querySelectorAll('.star-group input').forEach(input => {
    input.addEventListener('change', () => {
        const lbl = document.getElementById('ratingLabel');
        lbl.textContent = ratingLabels[input.value] || '';
        lbl.style.color = ratingColors[input.value] || '';
        lbl.style.fontWeight = '700';
    });
});

/* ── CHARACTER COUNTER ────────────────────────────── */
function updateChar(el) {
    const count = el.value.length;
    const span  = document.getElementById('charCount');
    span.textContent = count + ' / 1000';
    span.style.color = count > 900 ? '#ef4444' : count > 700 ? '#f97316' : '#94a3b8';
}

/* ── FORM SUBMIT LOADING ──────────────────────────── */
document.getElementById('fbForm').addEventListener('submit', () => {
    const btn = document.getElementById('fbSubmit');
    btn.disabled    = true;
    btn.innerHTML   = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
    </svg> Submitting…`;
});

/* ── SCROLL REVEAL ────────────────────────────────── */
(function () {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
})();
</script>
@endsection
