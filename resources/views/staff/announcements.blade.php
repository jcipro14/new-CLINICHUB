@extends('layouts.portal')
@section('title','Announcements – UM Clinic')
@section('page_title','Announcements')

@section('content')
<div x-data="{ showAdd: false, showTemplates: false }" @open-add.window="showAdd = true">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Announcements</h2>
            <p class="text-sm text-slate-500 mt-0.5">Post and manage clinic announcements for all students</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showTemplates = !showTemplates"
                    class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:bg-amber-50 hover:border-amber-300 active:scale-95 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
                {{ $seasonIcon }} Health Alert Templates
            </button>
            <button @click="showAdd = true"
                    class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Post Announcement
            </button>
        </div>
    </div>

    {{-- Health Alert Templates Panel --}}
    <div x-show="showTemplates" x-cloak style="display:none"
         class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-5">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-amber-800 text-sm flex items-center gap-2">
                {{ $seasonIcon }} {{ $seasonLabel }} — Quick Alert Templates
                <span class="text-xs font-normal text-amber-600">(click to auto-fill the post form)</span>
            </h3>
            <button @click="showTemplates = false" class="text-amber-500 hover:text-amber-800 text-lg leading-none">&times;</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($templates as $tpl)
            <button type="button"
                    onclick="fillAnnouncementTemplate({{ json_encode($tpl['title']) }}, {{ json_encode($tpl['body']) }})"
                    class="text-left flex items-center gap-2.5 px-3 py-2.5 bg-white hover:bg-red-50 hover:border-red-200 border border-amber-200 rounded-xl transition text-sm font-semibold text-slate-700">
                <span>📋</span> {{ $tpl['label'] }}
                <svg class="w-3.5 h-3.5 ml-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            @endforeach
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-4 py-3 flex items-center gap-2 mb-4">
        <span>✅</span> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded-xl px-4 py-3 mb-4">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    @forelse($announcements as $ann)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3 flex-1">
                <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center text-lg shrink-0">📢</div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-800">{{ $ann->title }}</h3>
                    <p class="text-slate-600 text-sm mt-1.5 leading-relaxed">{{ $ann->body }}</p>
                    <div class="flex items-center gap-3 mt-3 text-xs text-slate-400">
                        <span>Posted by: <strong class="text-slate-600">{{ $ann->poster?->full_name ?? $ann->posted_by }}</strong></span>
                        <span>•</span>
                        <span>{{ $ann->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('staff.announcements.destroy', $ann->id) }}" onsubmit="return confirm('Delete this announcement?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition shrink-0">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 py-16 text-center">
        <div class="text-4xl mb-3">📢</div>
        <p class="text-slate-500 font-medium">No announcements yet</p>
        <button @click="showAdd=true" class="mt-3 text-sm text-red-700 hover:underline font-semibold">Post the first announcement →</button>
    </div>
    @endforelse

    @if($announcements->hasPages())
    <div class="mt-4">{{ $announcements->links() }}</div>
    @endif

    {{-- ADD MODAL --}}
    <div x-show="showAdd" x-cloak class="portal-modal-overlay" @click.self="showAdd=false" style="display:none">
        <div class="portal-modal-box">
            <div class="portal-modal-header">
                <h3>📢 Post Announcement</h3>
                <button @click="showAdd=false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('staff.announcements.store') }}">
                @csrf
                <div class="portal-modal-body space-y-0">
                    <div class="f-group">
                        <label class="f-label">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="annTitle" class="f-input" placeholder="Announcement title" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Message <span class="text-red-500">*</span></label>
                        <textarea name="body" id="annBody" class="f-textarea" rows="5" placeholder="Write your announcement..." required></textarea>
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showAdd=false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function fillAnnouncementTemplate(title, body) {
    document.getElementById('annTitle').value = title;
    document.getElementById('annBody').value  = body;
    // Open the post modal and scroll to it
    window.dispatchEvent(new CustomEvent('open-add'));
}
</script>
@endsection
