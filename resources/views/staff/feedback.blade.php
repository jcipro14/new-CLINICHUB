@extends('layouts.portal')
@section('title','Student Feedback – UM Clinic')
@section('page_title','Student Feedback')

@section('content')

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Student Feedback</h2>
        <p class="text-sm text-slate-500 mt-0.5">All feedback submitted by students about the clinic</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Total Feedback</div>
        <div class="text-3xl font-extrabold text-red-700">{{ $totalFeedback }}</div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Average Rating</div>
        <div class="text-3xl font-extrabold text-amber-500">
            {{ $avgRating ? number_format($avgRating, 1) : '—' }}
            @if($avgRating)<span class="text-xl">⭐</span>@endif
        </div>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100 col-span-2 sm:col-span-1">
        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1">Showing</div>
        <div class="text-3xl font-extrabold text-slate-700">{{ $feedbacks->total() }}</div>
        <div class="text-xs text-slate-400 mt-0.5">{{ $search ? 'matching results' : 'all records' }}</div>
    </div>
</div>

{{-- Search --}}
<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ $search }}"
           placeholder="Search by student name or message..."
           class="f-input max-w-sm">
    <button type="submit" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Search</button>
    @if($search)
    <a href="{{ route('staff.feedback') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition">Clear</a>
    @endif
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    @if($feedbacks->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-slate-400">
        <span class="text-4xl mb-3">💬</span>
        <p class="font-medium text-sm">No feedback yet</p>
        <p class="text-xs mt-1">Student feedback will appear here after they submit it.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr><th>Student</th><th>Rating</th><th>Message</th><th>Date</th></tr>
            </thead>
            <tbody>
                @foreach($feedbacks as $fb)
                <tr>
                    <td class="font-medium whitespace-nowrap">{{ $fb->name ?? $fb->student_id }}</td>
                    <td>
                        @if($fb->rating)
                        <span class="flex items-center gap-0.5 whitespace-nowrap">
                            @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $fb->rating ? 'text-yellow-400' : 'text-slate-200' }} text-base leading-none">★</span>
                            @endfor
                        </span>
                        @else
                        <span class="text-slate-400 text-xs">No rating</span>
                        @endif
                    </td>
                    <td class="text-slate-600">{{ $fb->message }}</td>
                    <td class="text-xs text-slate-400 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($fb->created_at)->format('M d, Y g:i A') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $feedbacks->appends(['search' => $search])->links() }}
    </div>
    @endif
</div>

@endsection
