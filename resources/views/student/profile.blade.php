@extends('layouts.portal')
@section('title','My Profile – UM Clinic')
@section('page_title','My Profile')

@section('styles')
<style>
/* ── ANIMATIONS ─────────────────────────────────── */
@keyframes fadeUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes countUp { from { opacity:0; transform:scale(.8); } to { opacity:1; transform:scale(1); } }
@keyframes avatarPop { from { opacity:0; transform:scale(.7) rotate(-6deg); } to { opacity:1; transform:scale(1) rotate(0); } }

.afu  { animation: fadeUp .5s cubic-bezier(.4,0,.2,1) both; }
.d1{animation-delay:.05s} .d2{animation-delay:.10s} .d3{animation-delay:.16s}
.d4{animation-delay:.22s} .d5{animation-delay:.28s}

/* ── SCROLL REVEAL ───────────────────────────────── */
.reveal { opacity:0; transform:translateY(20px); transition: opacity .5s ease, transform .5s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }

/* ── PROFILE HERO ────────────────────────────────── */
.profile-hero { position:relative; border-radius:1.4rem; overflow:hidden; }
.hero-cover {
    height: 145px;
    position: relative;
    overflow: hidden;
}
.hero-cover::after {
    content:'';
    position:absolute; inset:0;
    background: linear-gradient(to bottom, transparent 40%, rgba(0,0,0,.25) 100%);
}

/* ── AVATAR ──────────────────────────────────────── */
.avatar-ring {
    width: 88px; height: 88px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.2rem; font-weight: 900; color: #fff;
    border: 4px solid #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,.18);
    animation: avatarPop .5s cubic-bezier(.4,0,.2,1) .15s both;
    position: relative; z-index: 10; flex-shrink: 0;
}

/* ── STAT CARDS ──────────────────────────────────── */
.stat-c {
    background: #fff; border-radius: 1.2rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    padding: 1.1rem; text-align:center;
    transition: transform .2s, box-shadow .2s;
}
.stat-c:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.09); }
.stat-val { font-size:1.9rem; font-weight:900; line-height:1; animation: countUp .5s ease both; }

/* ── INFO ROWS ───────────────────────────────────── */
.info-row {
    display: flex; align-items: flex-start; gap: .9rem;
    padding: .85rem 0; border-bottom: 1px solid #f8fafc;
}
.info-row:last-child { border: none; padding-bottom: 0; }
.info-icon {
    width: 2.1rem; height: 2.1rem; border-radius: .6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}

/* ── PASSWORD INPUT WRAPPER ──────────────────────── */
.pwd-wrap { position: relative; }
.pwd-wrap input { padding-right: 2.75rem; }
.pwd-eye {
    position: absolute; right: .85rem; top: 50%; transform: translateY(-50%);
    color: #94a3b8; cursor: pointer; transition: color .15s;
}
.pwd-eye:hover { color: #991b1b; }

/* ── STRENGTH BARS ───────────────────────────────── */
.str-track { height:4px; background:#f1f5f9; border-radius:4px; overflow:hidden; flex:1; }
.str-fill  { height:4px; border-radius:4px; width:0; transition:width .3s ease, background .3s ease; }

/* ── PANEL CARD ──────────────────────────────────── */
.p-card {
    background: #fff; border-radius: 1.2rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    overflow: hidden;
    transition: box-shadow .2s;
}
.p-card:hover { box-shadow: 0 6px 22px rgba(0,0,0,.07); }
.p-card-head {
    display: flex; align-items: center; gap: .75rem;
    padding: 1rem 1.25rem; border-bottom: 1px solid #f8fafc;
}
.p-card-icon {
    width: 2rem; height: 2rem; border-radius: .55rem;
    display: flex; align-items: center; justify-content: center; font-size: .95rem;
}
</style>
@endsection

@section('content')

@php
$_theme = $settings->active_theme ?? 'default';
$_grad  = match($_theme) {
    'christmas'        => ['from-green-800 to-green-950',   'from-green-700 to-green-900'],
    'summer'           => ['from-amber-600 to-orange-800',  'from-amber-500 to-orange-700'],
    'rainy_season'     => ['from-blue-800 to-blue-950',     'from-blue-700 to-blue-900'],
    'holy_week'        => ['from-purple-800 to-purple-950', 'from-purple-700 to-purple-900'],
    'undas'            => ['from-stone-700 to-stone-900',   'from-stone-600 to-stone-800'],
    'new_year'         => ['from-indigo-800 to-indigo-950', 'from-indigo-700 to-indigo-900'],
    'independence_day' => ['from-blue-800 to-red-900',      'from-blue-700 to-red-800'],
    'halloween'        => ['from-orange-800 to-stone-950',  'from-orange-700 to-red-900'],
    default            => ['from-red-900 to-red-950',       'from-red-700 to-red-900'],
};
@endphp

{{-- ══════════════════ PROFILE HERO ══════════════════ --}}
<div class="profile-hero bg-white shadow-sm border border-slate-100 afu overflow-hidden">

    {{-- ── COVER BANNER ── --}}
    <div class="hero-cover bg-gradient-to-br {{ $_grad[0] }} relative">
        {{-- Subtle light overlay --}}
        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse 70% 80% at 20% 50%,rgba(255,255,255,.09),transparent),
                    radial-gradient(ellipse 40% 60% at 90% 10%,rgba(255,255,255,.06),transparent)"></div>
        {{-- Watermark initials — white & very faint --}}
        <div class="absolute right-6 bottom-0 translate-y-1/4 select-none pointer-events-none"
             style="font-size:7.5rem;font-weight:900;line-height:1;letter-spacing:-.04em;
                    color:rgba(255,255,255,.07);">
            {{ strtoupper(substr($user->first_name,0,1)) }}{{ strtoupper(substr($user->last_name,0,1)) }}
        </div>
        {{-- Clinic badge top-right --}}
        <div class="absolute top-3 right-4">
            <span class="inline-flex items-center gap-1.5 bg-white/12 backdrop-blur-sm border border-white/20
                          text-white text-[.65rem] font-bold px-2.5 py-1 rounded-full">
                🏥 UM Clinic Portal
            </span>
        </div>
    </div>

    {{-- ── PROFILE INFO ── --}}
    <div class="px-6 pb-5 -mt-10 relative z-10">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">

            {{-- LEFT: Avatar + Name block --}}
            <div class="flex items-end gap-4">

                {{-- Avatar --}}
                <div class="avatar-ring bg-gradient-to-br {{ $_grad[1] }} shrink-0">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}
                </div>

                {{-- Name + meta --}}
                <div class="pb-0.5 min-w-0">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 leading-tight">
                        {{ $user->full_name }}
                    </h2>

                    {{-- Course + role row --}}
                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                        <span class="badge-role student" style="font-size:.62rem">STUDENT</span>
                        <span class="text-[.72rem] text-slate-500 font-medium">{{ $user->course_label }}</span>
                    </div>

                    {{-- ID + Email chips --}}
                    <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                        <span class="inline-flex items-center gap-1 text-[.67rem] font-semibold
                                     text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                            </svg>
                            {{ $user->id_number }}
                        </span>
                        @if($user->email)
                        <span class="inline-flex items-center gap-1 text-[.67rem] font-semibold
                                     text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full
                                     max-w-[220px] truncate">
                            <svg class="w-2.5 h-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="truncate">{{ $user->email }}</span>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT: Meta info --}}
            <div class="pb-0.5 flex flex-row sm:flex-col items-center sm:items-end gap-3 sm:gap-1.5">
                <div class="inline-flex items-center gap-1.5 text-[.67rem] font-bold
                             text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full pulse-dot"></span>
                    Active Account
                </div>
                <div class="text-right">
                    <div class="text-[.62rem] text-slate-400 font-bold uppercase tracking-wider">Member since</div>
                    <div class="text-xs font-bold text-slate-600 mt-0.5">
                        {{ $user->created_at?->format('F Y') ?? '—' }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ══════════════════ STATS ══════════════════ --}}
<div class="grid grid-cols-3 gap-4">
    @foreach([
        ['🏥','Total Visits',   $totalVisits,  'text-blue-700',   'bg-blue-50',   'All time'],
        ['📅','Appointments',   $totalAppts,   'text-amber-700',  'bg-amber-50',  'Total booked'],
        ['🗓','Last Visit',     $lastRecord?->date_consulted?->format('M d, Y') ?? '—', 'text-emerald-700','bg-emerald-50','Most recent'],
    ] as $ci => [$icon,$label,$val,$col,$bg,$sub])
    <div class="stat-c afu d{{ $ci+1 }}">
        <div class="w-10 h-10 {{ $bg }} rounded-xl flex items-center justify-center text-xl mx-auto mb-2.5">{{ $icon }}</div>
        <div class="stat-val {{ $col }}">{{ $val }}</div>
        <div class="text-xs font-bold text-slate-600 mt-1">{{ $label }}</div>
        <div class="text-[.65rem] text-slate-400 mt-0.5">{{ $sub }}</div>
    </div>
    @endforeach
</div>

{{-- ══════════════════ MAIN GRID ══════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

    {{-- ── ACCOUNT INFORMATION (3 cols) ── --}}
    <div class="lg:col-span-3 p-card reveal">
        <div class="p-card-head">
            <div class="p-card-icon bg-slate-100">👤</div>
            <div>
                <span class="font-black text-slate-800 text-sm">Account Information</span>
                <p class="text-[.67rem] text-slate-400 mt-0.5">Your details are managed by the clinic admin</p>
            </div>
        </div>
        <div class="px-5 py-4">

            {{-- Full Name --}}
            <div class="info-row">
                <div class="info-icon bg-red-50">👤</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Full Name</div>
                    <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $user->full_name }}</div>
                </div>
                <span class="text-[.65rem] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">Read-only</span>
            </div>

            {{-- Student ID --}}
            <div class="info-row">
                <div class="info-icon bg-blue-50">🪪</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Student ID</div>
                    <div class="text-sm font-bold text-slate-800 font-mono mt-0.5">{{ $user->id_number }}</div>
                </div>
                <span class="text-[.65rem] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">Read-only</span>
            </div>

            {{-- Course --}}
            <div class="info-row">
                <div class="info-icon bg-amber-50">🎓</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Course / Program</div>
                    <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $user->course_label }}</div>
                </div>
                <span class="text-[.65rem] text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">Read-only</span>
            </div>

            {{-- Email (read-only, no edit) --}}
            <div class="info-row">
                <div class="info-icon bg-violet-50">✉️</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Email Address</div>
                    <div class="text-sm font-bold text-slate-800 mt-0.5 truncate">
                        {{ $user->email ?: '—' }}
                    </div>
                    <div class="text-[.65rem] text-slate-400 mt-0.5">Contact admin to update your email</div>
                </div>
                @if($user->email)
                <span class="text-[.65rem] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full shrink-0">✓ Set</span>
                @else
                <span class="text-[.65rem] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full shrink-0">Not set</span>
                @endif
            </div>

            {{-- Account Role --}}
            <div class="info-row">
                <div class="info-icon bg-green-50">🔖</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Account Role</div>
                    <div class="mt-0.5"><span class="badge-role student text-[.67rem]">STUDENT</span></div>
                </div>
            </div>

            {{-- Member Since --}}
            <div class="info-row">
                <div class="info-icon bg-slate-100">📅</div>
                <div class="flex-1 min-w-0">
                    <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Member Since</div>
                    <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $user->created_at?->format('F j, Y') ?? '—' }}</div>
                </div>
            </div>

        </div>
    </div>

    {{-- ── CHANGE PASSWORD (2 cols) ── --}}
    <div class="lg:col-span-2 p-card reveal d2">
        <div class="p-card-head">
            <div class="p-card-icon bg-orange-100">🔒</div>
            <div>
                <span class="font-black text-slate-800 text-sm">Change Password</span>
                <p class="text-[.67rem] text-slate-400 mt-0.5">Keep your account secure</p>
            </div>
        </div>
        <div class="px-5 py-5">

            {{-- Success / error alerts --}}
            @if(session('success'))
            <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold px-4 py-3 rounded-xl">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->has('current_password'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-xl">
                ⚠ {{ $errors->first('current_password') }}
            </div>
            @endif
            @if($errors->has('password'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-4 py-3 rounded-xl">
                ⚠ {{ $errors->first('password') }}
            </div>
            @endif

            <form method="POST" action="{{ route('student.profile.password') }}" class="space-y-3.5">
                @csrf

                {{-- Current Password --}}
                <div>
                    <label class="block text-[.7rem] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Current Password <span class="text-red-500">*</span>
                    </label>
                    <div class="pwd-wrap">
                        <input type="password" name="current_password" id="pwd0"
                               class="f-input pr-10" autocomplete="current-password" required
                               placeholder="Enter current password">
                        <button type="button" class="pwd-eye" onclick="togglePwd('pwd0',this)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- New Password --}}
                <div>
                    <label class="block text-[.7rem] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        New Password <span class="text-red-500">*</span>
                    </label>
                    <div class="pwd-wrap">
                        <input type="password" name="password" id="pwd1"
                               class="f-input pr-10" autocomplete="new-password" required
                               placeholder="Min. 8 chars, upper, number, symbol"
                               oninput="updateStrength(this.value)">
                        <button type="button" class="pwd-eye" onclick="togglePwd('pwd1',this)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    {{-- Strength meter --}}
                    <div class="flex items-center gap-1.5 mt-2">
                        <div class="str-track"><div class="str-fill" id="sb1"></div></div>
                        <div class="str-track"><div class="str-fill" id="sb2"></div></div>
                        <div class="str-track"><div class="str-fill" id="sb3"></div></div>
                        <div class="str-track"><div class="str-fill" id="sb4"></div></div>
                        <span id="strLabel" class="text-[.65rem] font-bold text-slate-400 w-12 shrink-0"></span>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="block text-[.7rem] font-bold text-slate-500 uppercase tracking-wider mb-1.5">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="pwd-wrap">
                        <input type="password" name="password_confirmation" id="pwd2"
                               class="f-input pr-10" autocomplete="new-password" required
                               placeholder="Re-enter new password"
                               oninput="checkMatch()">
                        <button type="button" class="pwd-eye" onclick="togglePwd('pwd2',this)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="matchMsg" class="text-[.68rem] font-semibold mt-1 hidden"></p>
                </div>

                {{-- Password requirements --}}
                <div class="bg-slate-50 rounded-xl p-3 space-y-1.5">
                    <p class="text-[.67rem] font-bold text-slate-500 uppercase tracking-wider mb-1">Requirements</p>
                    @foreach([
                        ['len',  'At least 8 characters'],
                        ['up',   'One uppercase letter (A–Z)'],
                        ['num',  'One number (0–9)'],
                        ['sym',  'One special character (!@#$...)'],
                    ] as [$id, $req])
                    <div class="flex items-center gap-2" id="req-{{ $id }}">
                        <span class="w-4 h-4 rounded-full border-2 border-slate-300 flex items-center justify-center text-[.6rem] font-bold req-dot" id="dot-{{ $id }}"></span>
                        <span class="text-[.68rem] text-slate-500 req-txt" id="txt-{{ $id }}">{{ $req }}</span>
                    </div>
                    @endforeach
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-red-800 to-red-700 hover:from-red-900 hover:to-red-800 active:scale-[.99] text-white font-bold text-sm py-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════ CLINIC INFO ══════════════════ --}}
<div class="p-card reveal">
    <div class="p-card-head">
        <div class="p-card-icon bg-green-100">🏥</div>
        <span class="font-black text-slate-800 text-sm">Clinic Information</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
        <div class="flex items-center gap-3 px-5 py-4">
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-lg shrink-0">🏥</div>
            <div>
                <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Clinic Name</div>
                <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $settings->system_name }}</div>
            </div>
        </div>
        <div class="flex items-center gap-3 px-5 py-4">
            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-lg shrink-0">🕗</div>
            <div>
                <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Operating Hours</div>
                <div class="text-sm font-bold text-slate-800 mt-0.5">{{ $settings->clinic_hours ?: 'N/A' }}</div>
            </div>
        </div>
        <div class="flex items-center gap-3 px-5 py-4">
            <div class="w-10 h-10 {{ $settings->clinic_status === 'open' ? 'bg-emerald-50' : 'bg-red-50' }} rounded-xl flex items-center justify-center text-lg shrink-0">
                {{ $settings->clinic_status === 'open' ? '🟢' : '🔴' }}
            </div>
            <div>
                <div class="text-[.67rem] text-slate-400 font-bold uppercase tracking-wider">Current Status</div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-2 h-2 rounded-full pulse-dot {{ $settings->clinic_status === 'open' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                    <span class="text-sm font-black {{ $settings->clinic_status === 'open' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ strtoupper($settings->clinic_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── PASSWORD TOGGLE ──────────────────────────────── */
function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const isHidden = inp.type === 'password';
    inp.type = isHidden ? 'text' : 'password';
    btn.innerHTML = isHidden
        ? `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`
        : `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
}

/* ── STRENGTH METER ───────────────────────────────── */
function updateStrength(val) {
    const checks = {
        len: val.length >= 8,
        up:  /[A-Z]/.test(val),
        num: /[0-9]/.test(val),
        sym: /[\W_]/.test(val),
    };
    const score = Object.values(checks).filter(Boolean).length;
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Weak','Fair','Good','Strong'];

    ['sb1','sb2','sb3','sb4'].forEach((id, i) => {
        const bar = document.getElementById(id);
        bar.style.width      = i < score ? '100%' : '0';
        bar.style.background = i < score ? colors[score - 1] : 'transparent';
    });

    const lbl = document.getElementById('strLabel');
    lbl.textContent  = val.length ? (labels[score - 1] || 'Weak') : '';
    lbl.style.color  = val.length ? (colors[score - 1] || '#ef4444') : '';

    /* Requirement indicators */
    Object.entries(checks).forEach(([key, ok]) => {
        const dot = document.getElementById('dot-' + key);
        const txt = document.getElementById('txt-' + key);
        if (dot) {
            dot.style.background    = ok ? '#22c55e' : '';
            dot.style.borderColor   = ok ? '#22c55e' : '';
            dot.style.color         = ok ? '#fff' : '';
            dot.textContent         = ok ? '✓' : '';
        }
        if (txt) txt.style.color = ok ? '#16a34a' : '';
    });
}

/* ── CONFIRM MATCH ────────────────────────────────── */
function checkMatch() {
    const p1  = document.getElementById('pwd1').value;
    const p2  = document.getElementById('pwd2').value;
    const msg = document.getElementById('matchMsg');
    if (!p2.length) { msg.classList.add('hidden'); return; }
    const match = p1 === p2;
    msg.textContent  = match ? '✓ Passwords match' : '✗ Passwords do not match';
    msg.style.color  = match ? '#16a34a' : '#dc2626';
    msg.classList.remove('hidden');
}

/* ── SCROLL REVEAL ────────────────────────────────── */
(function () {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
})();
</script>
@endsection
