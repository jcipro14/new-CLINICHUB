@extends('layouts.portal')
@section('title','Message – UM Clinic')
@section('page_title','Clinic Inbox')

@section('styles')
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
.afu { animation: fadeUp .45s cubic-bezier(.4,0,.2,1) both; }
.d1  { animation-delay: .05s; }
.d2  { animation-delay: .10s; }
</style>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- ── BACK + TITLE ── --}}
    <div class="flex items-center justify-between mb-5 afu">
        <div>
            <h2 class="text-xl font-black text-slate-800">Message</h2>
            <p class="text-xs text-slate-400 mt-0.5">From the UM Clinic</p>
        </div>
        <a href="{{ route('student.messages') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-red-700 hover:border-red-700 hover:text-white text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Inbox
        </a>
    </div>

    {{-- ── MESSAGE CARD ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden afu d1">

        {{-- Subject banner --}}
        <div class="bg-gradient-to-br from-red-950 via-red-900 to-red-800 px-6 py-5 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,.07),transparent_60%)] pointer-events-none"></div>
            <div class="absolute -bottom-6 -right-6 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="relative z-10 flex items-start gap-3">
                <div class="w-9 h-9 bg-white/15 rounded-xl flex items-center justify-center text-lg shrink-0 mt-0.5">✉️</div>
                <div>
                    <h3 class="font-black text-white text-lg leading-snug">{{ $message->subject }}</h3>
                    <span class="inline-flex items-center gap-1 mt-1.5 text-red-300/80 text-[.68rem] font-semibold">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $message->sent_at->format('F j, Y \a\t g:i A') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Sender meta --}}
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-700 to-red-900 flex items-center justify-center text-white text-sm font-black shadow-sm shrink-0">
                {{ strtoupper(substr($message->sender?->first_name ?? $message->sender_id, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-800 text-sm">
                        {{ $message->sender?->full_name ?? $message->sender_id }}
                    </span>
                    @if($message->sender)
                    <span class="text-[.68rem] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full uppercase tracking-wide">
                        {{ $message->sender->role }}
                    </span>
                    @endif
                </div>
                <p class="text-[.7rem] text-slate-400 mt-0.5">Clinic Staff · UM Tagum City</p>
            </div>
            <div class="ml-auto">
                <span class="inline-flex items-center gap-1.5 text-[.68rem] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Read
                </span>
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-7">
            <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{!! nl2br(e($message->body)) !!}</div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/40 flex items-center justify-between">
            <span class="text-xs text-slate-400">Received {{ $message->sent_at->diffForHumans() }}</span>
            <a href="{{ route('student.messages') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold text-red-700 hover:text-red-900 transition">
                ← Back to Inbox
            </a>
        </div>
    </div>

</div>
@endsection
