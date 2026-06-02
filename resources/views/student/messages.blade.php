@extends('layouts.portal')
@section('title','Clinic Inbox – UM Clinic')
@section('page_title','Clinic Inbox')

@section('styles')
<style>
@keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
.msg-card { animation: fadeUp .4s cubic-bezier(.4,0,.2,1) both; }
.msg-row {
    display: flex; align-items: flex-start; gap: 1rem;
    padding: 1rem 1.25rem;
    text-decoration: none;
    transition: background .15s, transform .15s;
    position: relative;
    border-bottom: 1px solid #f1f5f9;
}
.msg-row:last-child { border-bottom: none; }
.msg-row:hover { background: #fdf2f2; transform: translateX(3px); }
.msg-row.unread { background: linear-gradient(90deg, #fff1f2 0%, #fff 100%); }
.msg-row.unread::before {
    content: '';
    position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px; background: #991b1b; border-radius: 0 2px 2px 0;
}
.avatar {
    width: 2.5rem; height: 2.5rem; border-radius: 50%;
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .8rem; font-weight: 800;
    flex-shrink: 0; box-shadow: 0 2px 8px rgba(153,27,27,.25);
}
.unread-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #991b1b; flex-shrink: 0;
    box-shadow: 0 0 0 2px rgba(153,27,27,.2);
}
.stat-chip {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1rem; border-radius: .8rem;
    font-size: .78rem; font-weight: 700;
}
</style>
@endsection

@section('content')
@php $unread = $inbox->where('is_read', false)->count(); @endphp

{{-- ── HERO HEADER ── --}}
<div class="relative bg-gradient-to-br from-red-950 via-red-900 to-red-800 rounded-2xl overflow-hidden shadow-lg msg-card">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,.06),transparent_60%)] pointer-events-none"></div>
    <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="relative z-10 flex items-center justify-between px-6 py-5 gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <div class="w-8 h-8 bg-white/15 rounded-xl flex items-center justify-center text-base">✉️</div>
                <h2 class="text-white font-black text-lg">Clinic Inbox</h2>
            </div>
            <p class="text-red-300/80 text-xs">Messages sent to you by the UM Clinic staff</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="text-center px-4 py-2 bg-white/10 rounded-xl border border-white/15">
                <div class="text-white font-black text-xl leading-none">{{ $inbox->count() }}</div>
                <div class="text-red-300/80 text-[.65rem] font-semibold uppercase tracking-wide mt-0.5">Total</div>
            </div>
            @if($unread > 0)
            <div class="text-center px-4 py-2 bg-red-600/40 rounded-xl border border-red-400/30">
                <div class="text-white font-black text-xl leading-none">{{ $unread }}</div>
                <div class="text-red-300/80 text-[.65rem] font-semibold uppercase tracking-wide mt-0.5">Unread</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── INBOX LIST ── --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden msg-card" style="animation-delay:.08s">

    @if($inbox->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-center px-6">
        <div class="w-20 h-20 bg-gradient-to-br from-red-50 to-rose-100 rounded-3xl flex items-center justify-center text-4xl mb-4 shadow-inner">
            ✉️
        </div>
        <h3 class="font-bold text-slate-700 text-base mb-1">Your inbox is empty</h3>
        <p class="text-slate-400 text-sm max-w-xs">When the clinic sends you a message, it will appear here.</p>
    </div>

    @else
    {{-- Toolbar --}}
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 bg-slate-50/60">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
            {{ $inbox->count() }} message{{ $inbox->count() !== 1 ? 's' : '' }}
        </span>
        @if($unread > 0)
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-700 bg-red-50 border border-red-200 px-2.5 py-1 rounded-full">
            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
            {{ $unread }} unread
        </span>
        @else
        <span class="text-xs text-emerald-600 font-semibold">✓ All read</span>
        @endif
    </div>

    <div>
        @foreach($inbox as $i => $msg)
        <a href="{{ route('student.messages.show', $msg->id) }}"
           class="msg-row {{ !$msg->is_read ? 'unread' : '' }}"
           style="animation:fadeUp .4s cubic-bezier(.4,0,.2,1) {{ $i * 40 }}ms both">

            {{-- Avatar --}}
            <div class="avatar mt-0.5">
                {{ strtoupper(substr($msg->sender?->first_name ?? $msg->sender_id, 0, 1)) }}
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <span class="text-sm font-bold text-slate-800 truncate block">
                            {{ $msg->sender?->full_name ?? $msg->sender_id }}
                            @if($msg->sender)
                            <span class="text-[.68rem] font-semibold text-slate-400 ml-1 bg-slate-100 px-1.5 py-0.5 rounded-full align-middle">{{ strtoupper($msg->sender->role) }}</span>
                            @endif
                        </span>
                    </div>
                    <span class="text-[.68rem] text-slate-400 whitespace-nowrap shrink-0 mt-0.5 font-medium">
                        {{ $msg->sent_at->format('M d, Y') }}
                    </span>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    @if(!$msg->is_read)
                    <span class="unread-dot"></span>
                    @endif
                    <p class="text-sm {{ !$msg->is_read ? 'font-bold text-slate-800' : 'font-medium text-slate-600' }} truncate">
                        {{ $msg->subject }}
                    </p>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 truncate">{{ Str::limit($msg->body, 90) }}</p>
            </div>

            {{-- Arrow --}}
            <svg class="w-4 h-4 text-slate-300 group-hover:text-red-400 shrink-0 mt-1.5 transition"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @endforeach
    </div>
    @endif
</div>

@endsection
