@extends('layouts.portal')
@section('title','Message – UM Clinic')
@section('page_title','Messages')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Message</h2>
        </div>
        <a href="{{ route('staff.messages') }}"
           class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl transition shadow-sm">
            ← Back to Messages
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-lg">{{ $message->subject }}</h3>
        </div>
        <div class="px-6 py-5 space-y-2 border-b border-slate-100 bg-slate-50">
            <div class="flex gap-2 text-sm">
                <span class="font-semibold text-slate-500 w-14 shrink-0">From:</span>
                <span class="text-slate-800 font-medium">
                    {{ $message->sender?->full_name ?? $message->sender_id }}
                    @if($message->sender)
                    <span class="ml-1 text-xs bg-slate-200 text-slate-600 font-semibold px-1.5 py-0.5 rounded-full">{{ strtoupper($message->sender->role) }}</span>
                    @endif
                </span>
            </div>
            <div class="flex gap-2 text-sm">
                <span class="font-semibold text-slate-500 w-14 shrink-0">To:</span>
                <span class="text-slate-800 font-medium">
                    {{ $message->receiver?->full_name ?? $message->receiver_id }}
                    @if($message->receiver)
                    <span class="ml-1 text-xs bg-slate-200 text-slate-600 font-semibold px-1.5 py-0.5 rounded-full">{{ strtoupper($message->receiver->role) }}</span>
                    @endif
                </span>
            </div>
            <div class="flex gap-2 text-sm">
                <span class="font-semibold text-slate-500 w-14 shrink-0">Date:</span>
                <span class="text-slate-600">{{ $message->sent_at->format('F j, Y g:i A') }}</span>
            </div>
        </div>
        <div class="px-6 py-6">
            <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-wrap">{!! nl2br(e($message->body)) !!}</div>
        </div>
    </div>
</div>
@endsection
