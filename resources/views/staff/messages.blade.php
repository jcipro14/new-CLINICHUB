@extends('layouts.portal')
@section('title','Messages – UM Clinic')
@section('page_title','Messages')

@section('content')
<div x-data="{ showCompose: false }">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Messages</h2>
            <p class="text-sm text-slate-500 mt-0.5">Inbox and sent messages</p>
        </div>
        <button @click="showCompose = true"
                class="inline-flex items-center gap-2 bg-red-700 hover:bg-red-800 active:scale-95 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Compose
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl mb-5 w-fit">
        <a href="?tab=inbox" class="px-4 py-2 rounded-lg text-sm font-semibold transition
            {{ ($tab ?? 'inbox') === 'inbox' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
            Inbox
            @php $unread = $inbox->where('is_read', false)->count(); @endphp
            @if($unread > 0)
            <span class="ml-1.5 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $unread }}</span>
            @endif
        </a>
        <a href="?tab=sent" class="px-4 py-2 rounded-lg text-sm font-semibold transition
            {{ ($tab ?? 'inbox') === 'sent' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500 hover:text-slate-700' }}">
            Sent
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            @if(($tab ?? 'inbox') === 'inbox')
            <table class="portal-table">
                <thead><tr><th>From</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($inbox as $msg)
                    <tr class="{{ !$msg->is_read ? 'bg-red-50/50' : '' }}">
                        <td>
                            <span class="{{ !$msg->is_read ? 'font-semibold text-slate-800' : 'text-slate-600' }}">
                                {{ $msg->sender?->full_name ?? $msg->sender_id }}
                            </span>
                            @if($msg->sender)
                            <span class="ml-1 text-xs text-slate-400">({{ strtoupper($msg->sender->role) }})</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('staff.messages.show', $msg->id) }}" class="text-red-700 hover:underline {{ !$msg->is_read ? 'font-semibold' : '' }}">
                                {{ $msg->subject }}
                                @if(!$msg->is_read) <span class="inline-block w-2 h-2 bg-red-500 rounded-full ml-1 align-middle"></span> @endif
                            </a>
                        </td>
                        <td class="text-xs text-slate-400 whitespace-nowrap">{{ $msg->sent_at->format('M d, Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('staff.messages.destroy', $msg->id) }}" onsubmit="return confirm('Delete message?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-10 text-slate-400">Inbox is empty.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <table class="portal-table">
                <thead><tr><th>To</th><th>Subject</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($sent as $msg)
                    <tr>
                        <td>
                            {{ $msg->receiver?->full_name ?? $msg->receiver_id }}
                            @if($msg->receiver)
                            <span class="ml-1 text-xs text-slate-400">({{ strtoupper($msg->receiver->role) }})</span>
                            @endif
                        </td>
                        <td><a href="{{ route('staff.messages.show', $msg->id) }}" class="text-red-700 hover:underline">{{ $msg->subject }}</a></td>
                        <td class="text-xs text-slate-400 whitespace-nowrap">{{ $msg->sent_at->format('M d, Y H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('staff.messages.destroy', $msg->id) }}" onsubmit="return confirm('Unsend message?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-2.5 py-1 rounded-lg transition">Unsend</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-10 text-slate-400">No sent messages.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- COMPOSE MODAL --}}
    <div x-show="showCompose" x-cloak class="portal-modal-overlay" @click.self="showCompose=false" style="display:none">
        <div class="portal-modal-box">
            <div class="portal-modal-header">
                <h3>✉️ Compose Message</h3>
                <button @click="showCompose=false" class="text-slate-400 hover:text-slate-700 text-xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('staff.messages.send') }}">
                @csrf
                <div class="portal-modal-body space-y-0">
                    <div class="f-group">
                        <label class="f-label">To <span class="text-red-500">*</span></label>
                        <select name="receiver" class="f-select" required>
                            <option value="">— Select Recipient —</option>
                            @foreach($contacts->groupBy('role') as $role => $group)
                            <optgroup label="{{ strtoupper($role) }}">
                                @foreach($group as $c)
                                <option value="{{ $c->id_number }}">{{ $c->full_name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Subject <span class="text-red-500">*</span></label>
                        <input type="text" name="subject" class="f-input" placeholder="Subject" required>
                    </div>
                    <div class="f-group">
                        <label class="f-label">Message <span class="text-red-500">*</span></label>
                        <textarea name="body" class="f-textarea" rows="5" required></textarea>
                    </div>
                </div>
                <div class="portal-modal-footer">
                    <button type="button" @click="showCompose=false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 hover:bg-red-800 text-white text-sm font-semibold transition">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
