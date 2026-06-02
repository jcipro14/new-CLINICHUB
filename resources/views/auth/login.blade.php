<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UM Clinic – Sign In</title>
    <link rel="icon" type="image/png" href="{{ asset('images/um_logo_no_bg.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        *,*::before,*::after{font-family:'Inter',sans-serif;box-sizing:border-box;margin:0;padding:0}

        /* ── PAGE ─────────────────────────────────────── */
        html,body{height:100%;overflow:hidden}
        body{background:#0a0101;min-height:100vh;display:flex;overflow-x:hidden}

        /* ── KEYFRAMES ────────────────────────────────── */
        @keyframes floatY  {0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
        @keyframes ringOut {0%{transform:scale(1);opacity:.55}100%{transform:scale(2.4);opacity:0}}
        @keyframes pulseDot{0%,100%{box-shadow:0 0 0 0 rgba(252,165,165,.7)}60%{box-shadow:0 0 0 8px transparent}}
        @keyframes blobM   {
            0%,100%{border-radius:62% 38% 34% 66%/56% 33% 67% 44%}
            35%    {border-radius:30% 70% 68% 32%/47% 64% 36% 53%}
            68%    {border-radius:72% 28% 47% 53%/29% 62% 38% 71%}
        }
        @keyframes gradS   {0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
        @keyframes fadeUp  {from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
        @keyframes panelIn {from{opacity:0;transform:translateY(18px) scale(.98)}to{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes panelOut{from{opacity:1;transform:translateY(0)}to{opacity:0;transform:translateY(-14px)}}
        @keyframes shake   {0%,100%{transform:translateX(0)}20%{transform:translateX(-7px)}40%{transform:translateX(7px)}60%{transform:translateX(-4px)}80%{transform:translateX(4px)}}
        @keyframes spin    {to{transform:rotate(360deg)}}
        @keyframes marqR   {from{transform:translateX(-50%)}to{transform:translateX(0)}}
        @keyframes shimmer {from{background-position:-200% 0}to{background-position:200% 0}}

        .panel-in {animation:panelIn .38s cubic-bezier(.22,1,.36,1) both}
        .panel-out{animation:panelOut .22s cubic-bezier(.4,0,.2,1) forwards}
        .shake     {animation:shake .38s ease}
        .spin      {animation:spin .7s linear infinite}
        .afu       {animation:fadeUp .5s cubic-bezier(.22,1,.36,1) both}

        /* ── FULL-SCREEN LAYOUT ───────────────────────── */
        .login-card{
            display:flex;width:100vw;min-height:100vh;
            border-radius:0;overflow:hidden;
        }

        /* ══════════════════════════════════════════════
           LEFT — FORM PANEL (white)
        ══════════════════════════════════════════════ */
        .form-panel{
            width:460px;flex-shrink:0;
            background:#fff;
            display:flex;flex-direction:column;
            min-height:100vh;
            overflow-y:auto;
        }
        @media(max-width:800px){
            .login-card{flex-direction:column}
            .form-panel{width:100%;min-height:100vh}
            .visual-panel{display:none}
        }

        /* inputs */
        .um-input{
            width:100%;background:#f8fafc;
            border:1.5px solid #e2e8f0;border-radius:.7rem;
            color:#1e293b;font-size:.875rem;
            padding:.6rem .85rem;
            transition:border-color .2s,box-shadow .2s,background .2s;
            -webkit-appearance:none;appearance:none;
        }
        .um-input:focus{
            outline:none;background:#fff;
            border-color:#991b1b;
            box-shadow:0 0 0 3px rgba(153,27,27,.1);
        }
        .um-input::placeholder{color:#94a3b8}
        .um-input:-webkit-autofill,
        .um-input:-webkit-autofill:hover,
        .um-input:-webkit-autofill:focus{
            -webkit-box-shadow:0 0 0 1000px #f8fafc inset !important;
            -webkit-text-fill-color:#1e293b !important;
        }
        select.um-input option{background:#fff;color:#1e293b}

        /* labels */
        .f-label{display:block;font-size:.72rem;font-weight:700;color:#64748b;margin-bottom:.35rem;letter-spacing:.05em;text-transform:uppercase}

        /* input icon */
        .inp-icon{color:#94a3b8}
        .inp-icon-wrap{position:relative}
        .inp-icon-wrap .inp-icon{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);pointer-events:none}
        .inp-icon-wrap input{padding-left:2.5rem}

        /* role pills */
        .role-pill input[type=radio]{display:none}
        .role-pill label{
            display:inline-flex;align-items:center;justify-content:center;
            padding:.28rem .85rem;border-radius:999px;
            font-size:.73rem;font-weight:600;cursor:pointer;
            border:1.5px solid #e2e8f0;color:#64748b;
            transition:all .18s;white-space:nowrap;user-select:none;
        }
        .role-pill input:checked + label{
            background:#7f1d1d;border-color:#7f1d1d;color:#fff;
            box-shadow:0 3px 10px rgba(127,29,29,.3);
        }
        .role-pill label:hover{border-color:#991b1b;color:#991b1b}

        /* primary button */
        .btn-primary{
            background:linear-gradient(135deg,#7f1d1d,#991b1b);
            color:#fff;font-weight:700;font-size:.875rem;
            border-radius:.7rem;padding:.7rem;
            transition:all .25s cubic-bezier(.22,1,.36,1);
            position:relative;overflow:hidden;
        }
        .btn-primary:hover{
            background:linear-gradient(135deg,#991b1b,#b91c1c);
            transform:translateY(-1px);
            box-shadow:0 8px 24px rgba(153,27,27,.38);
        }
        .btn-primary:active{transform:scale(.98)}

        /* ripple */
        .ripple{position:absolute;border-radius:50%;background:rgba(255,255,255,.28);transform:scale(0);pointer-events:none;animation:rippleA .55s linear forwards}
        @keyframes rippleA{to{transform:scale(5);opacity:0}}

        /* eye toggle */
        .eye-btn{color:#94a3b8;transition:color .18s;position:absolute;right:.85rem;top:50%;transform:translateY(-50%)}
        .eye-btn:hover{color:#991b1b}

        /* alerts */
        .alert-err{background:#fef2f2;border:1.5px solid #fecaca;color:#991b1b;border-radius:.75rem;padding:.65rem 1rem;font-size:.8rem;display:flex;align-items:flex-start;gap:.6rem}
        .alert-ok {background:#f0fdf4;border:1.5px solid #bbf7d0;color:#166534;border-radius:.75rem;padding:.65rem 1rem;font-size:.8rem;display:flex;align-items:center;gap:.6rem}

        /* strength bars */
        .str-bar{height:3px;border-radius:2px;transition:width .3s,background .3s}

        /* divider */
        .divider{display:flex;align-items:center;gap:.75rem;color:#cbd5e1;font-size:.72rem}
        .divider::before,.divider::after{content:'';flex:1;height:1px;background:#f1f5f9}

        /* info block (bottom of form) */
        .info-block{background:#f8fafc;border:1.5px solid #f1f5f9;border-radius:.9rem;padding:.85rem 1rem}

        /* link style */
        .lnk{color:#991b1b;font-weight:700;transition:color .18s}
        .lnk:hover{color:#7f1d1d;text-decoration:underline;text-underline-offset:2px}

        /* scrollbar */
        ::-webkit-scrollbar{width:4px}
        ::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px}

        /* ══════════════════════════════════════════════
           RIGHT — VISUAL PANEL (crimson)
        ══════════════════════════════════════════════ */
        .visual-panel{
            flex:1;position:relative;overflow:hidden;
            background:linear-gradient(145deg,#3b0000,#7f1d1d,#9f1239);
            background-size:300% 300%;
            animation:gradS 10s ease infinite;
            min-height:100vh;
        }
        #vCanvas{position:absolute;inset:0;pointer-events:none;z-index:1}
        .v-grid{
            position:absolute;inset:0;z-index:2;pointer-events:none;
            background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),
                             linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);
            background-size:46px 46px;
        }
        .v-vig{
            position:absolute;inset:0;z-index:3;pointer-events:none;
            background:radial-gradient(ellipse 80% 50% at 50% 110%,rgba(0,0,0,.45),transparent 65%),
                       radial-gradient(ellipse 60% 40% at 0% 0%,rgba(0,0,0,.2),transparent 50%);
        }
        .v-orb{
            position:absolute;border-radius:50%;pointer-events:none;z-index:2;
            animation:blobM linear infinite;
        }
        .v-content{position:relative;z-index:4;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2.5rem 2rem;text-align:center}

        /* logo glow ring */
        .logo-glow-wrap{position:relative;display:inline-flex;align-items:center;justify-content:center}
        .logo-pulse-ring{
            position:absolute;inset:-10px;border-radius:50%;
            border:2px solid rgba(252,165,165,.45);
            animation:ringOut 3s ease-out infinite;
        }
        .logo-pulse-ring-2{animation-delay:1.2s}
        .logo-pulse-ring-3{animation-delay:2.4s}

        /* feature pills */
        .v-feat{
            display:flex;align-items:center;gap:.75rem;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.16);
            border-radius:1rem;padding:.8rem 1.1rem;
            backdrop-filter:blur(8px);
            text-align:left;
            transition:background .2s;
        }
        .v-feat:hover{background:rgba(255,255,255,.16)}

        /* status dot */
        .v-dot{width:9px;height:9px;border-radius:50%;background:#4ade80;animation:pulseDot 2.5s ease-in-out infinite;flex-shrink:0}

        /* marquee ticker on visual panel */
        .v-ticker{overflow:hidden;position:relative;border-top:1px solid rgba(255,255,255,.1)}
        .v-ticker::before,.v-ticker::after{content:'';position:absolute;top:0;bottom:0;width:40px;z-index:2;pointer-events:none}
        .v-ticker::before{left:0;background:linear-gradient(90deg,rgba(63,0,0,.6),transparent)}
        .v-ticker::after {right:0;background:linear-gradient(-90deg,rgba(100,10,20,.6),transparent)}
        .v-tick-track{display:flex;width:max-content;animation:marqR 30s linear infinite}
        .v-tick-item{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 2rem;font-size:.72rem;color:rgba(255,255,255,.65);white-space:nowrap;font-weight:500}
        .v-tick-sep{width:4px;height:4px;border-radius:50%;background:rgba(255,165,165,.5);flex-shrink:0}

        /* ── INACTIVE MODAL ───────────────────────── */
        .modal-glass{background:rgba(255,255,255,.96);backdrop-filter:blur(24px);border:1.5px solid #fecaca;border-radius:1.4rem}
    </style>
</head>
<body>

{{-- INACTIVE LOGOUT MODAL --}}
@if($inactiveLogout ?? false)
<div id="inactiveModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="modal-glass p-8 max-w-sm w-full text-center shadow-2xl">
        <div class="w-14 h-14 rounded-full bg-red-50 border-2 border-red-200 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Session Expired</h3>
        <p class="text-slate-500 text-sm mb-6">You were logged out due to inactivity. Please sign in again.</p>
        <button onclick="document.getElementById('inactiveModal').remove()"
                class="btn-primary w-full py-2.5 rounded-xl text-sm font-bold">OK, Got It</button>
    </div>
</div>
@endif

{{-- ═══════════════════════════ FULL-SCREEN LAYOUT ═══════════════════════════ --}}
<div class="login-card afu" style="animation:fadeUp .6s cubic-bezier(.22,1,.36,1) both">

    {{-- ════════════════════════════════════════
         LEFT: FORM PANEL
    ════════════════════════════════════════ --}}
    <div class="form-panel">

        {{-- ── TOP BRAND ── --}}
        <div class="px-8 pt-7 pb-5 border-b border-slate-50 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 border-2 border-red-100 rounded-xl flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="UM" class="w-7 h-7 object-contain">
                </div>
                <div>
                    <div class="font-black text-slate-800 text-sm leading-tight">UM Visayan Clinic</div>
                    <div class="text-[.65rem] text-slate-400 font-semibold uppercase tracking-wider">Tagum City Campus</div>
                </div>
            </div>
        </div>

        {{-- ── LOGIN PANEL ── --}}
        <div id="loginPanel" class="flex-1 flex flex-col px-8 py-8 justify-center">

            <div class="mb-5">
                <h1 class="text-slate-800 text-[1.3rem] font-black leading-tight">Welcome back 👋</h1>
                <p class="text-slate-400 text-sm mt-1">Sign in to your clinic portal</p>
            </div>

            {{-- Alerts --}}
            @if($errors->has('login'))
            <div class="alert-err shake mb-4">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 012 0v4a1 1 0 01-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first('login') }}</span>
            </div>
            @endif
            @if(session('success'))
            <div class="alert-ok mb-4">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4 flex-1" id="loginForm">
                @csrf

                {{-- ID Number --}}
                <div>
                    <label class="f-label">ID Number</label>
                    <div class="inp-icon-wrap relative">
                        <span class="inp-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c2.21 0 4 1.343 4 3H3c0-1.657 1.79-3 4-3z"/></svg>
                        </span>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                               placeholder="Enter your ID number" autocomplete="username"
                               class="um-input">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="f-label">Password</label>
                    <div class="inp-icon-wrap relative">
                        <span class="inp-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input type="password" id="loginPassword" name="password" required
                               placeholder="Enter your password" autocomplete="current-password"
                               class="um-input pr-11">
                        <button type="button" id="toggleLoginPwd" class="eye-btn">
                            <svg id="eyeOpen" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="eyeClosed" class="w-[18px] h-[18px] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label class="f-label">Sign in as</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['student'=>'Student','staff'=>'Staff','sta'=>'STA','superadmin'=>'Admin'] as $val=>$lbl)
                        <div class="role-pill">
                            <input type="radio" name="role" id="role_{{ $val }}" value="{{ $val }}"
                                   {{ old('role','student') === $val ? 'checked' : '' }}>
                            <label for="role_{{ $val }}">{{ $lbl }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" id="loginBtn"
                        class="btn-primary w-full flex items-center justify-center gap-2 mt-1">
                    <span id="loginBtnText">Sign In</span>
                    <svg id="loginArrow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    <svg id="loginSpinner" class="w-4 h-4 spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </button>
            </form>

            <p class="text-center text-slate-400 text-xs mt-4">
                Don't have an account?
                <button id="showRegisterBtn" class="lnk ml-1">Sign Up!</button>
            </p>

            <div class="divider my-4">or</div>

            {{-- Info block (reference format bottom section) --}}
            <div class="info-block">
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-slate-700 text-xs font-bold mb-0.5">Having trouble signing in?</p>
                        <p class="text-slate-400 text-xs leading-relaxed">Use your school-issued ID number as your username. Contact clinic staff if you need help accessing your account.</p>
                    </div>
                </div>
            </div>

            <p class="text-center text-slate-300 text-[.62rem] mt-4 tracking-wide">
                © {{ date('Y') }} University of Mindanao · Tagum City Campus
            </p>
        </div>

        {{-- ── REGISTER PANEL ── --}}
        <div id="registerPanel" class="hidden flex-1 flex flex-col px-8 py-6 justify-center">

            <div class="mb-4">
                <h1 class="text-slate-800 text-[1.2rem] font-black leading-tight">Create Account</h1>
                <p class="text-slate-400 text-sm mt-0.5">Students only · Free registration</p>
            </div>

            @if($errors->hasAny(['reg_id','reg_password','email','first_name','last_name']))
            <div class="alert-err shake mb-4 flex-col items-start gap-1">
                <div class="flex items-start gap-2 w-full">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 012 0v4a1 1 0 01-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
                    <div class="space-y-0.5 text-xs">
                        @foreach(['first_name','last_name','reg_id','email','reg_password'] as $field)
                            @error($field)<p>• {{ $message }}</p>@enderror
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-3 flex-1 overflow-y-auto">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="f-label">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                               placeholder="First name" class="um-input">
                    </div>
                    <div>
                        <label class="f-label">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                               placeholder="Last name" class="um-input">
                    </div>
                </div>

                <div>
                    <label class="f-label">Student ID Number</label>
                    <div class="inp-icon-wrap relative">
                        <span class="inp-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                        </span>
                        <input type="text" name="reg_id" value="{{ old('reg_id') }}" required
                               placeholder="e.g. 2021-00001" class="um-input">
                    </div>
                </div>

                <div>
                    <label class="f-label">Email Address</label>
                    <div class="inp-icon-wrap relative">
                        <span class="inp-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               placeholder="Gmail or EDU email" class="um-input">
                    </div>
                </div>

                <div>
                    <label class="f-label">Course / Program</label>
                    <select name="course" class="um-input">
                        <option value="">Select your course</option>
                        <option value="BSIT"  {{ old('course')==='BSIT'  ?'selected':'' }}>BS Information Technology</option>
                        <option value="BSCS"  {{ old('course')==='BSCS'  ?'selected':'' }}>BS Computer Science</option>
                        <option value="BSCpE" {{ old('course')==='BSCpE' ?'selected':'' }}>BS Computer Engineering</option>
                        <option value="BSEE"  {{ old('course')==='BSEE'  ?'selected':'' }}>BS Electrical Engineering</option>
                        <option value="BSECE" {{ old('course')==='BSECE' ?'selected':'' }}>BS Electronics Engineering</option>
                        <option value="DEE"   {{ old('course')==='DEE'   ?'selected':'' }}>Dept. of Engineering Education</option>
                    </select>
                </div>

                <div>
                    <label class="f-label">Password</label>
                    <div class="inp-icon-wrap relative">
                        <span class="inp-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input type="password" id="regPassword" name="reg_password" required
                               placeholder="Min. 8 chars, upper, number, symbol"
                               autocomplete="new-password"
                               class="um-input pr-11"
                               oninput="checkStrength(this.value)">
                        <button type="button" id="toggleRegPwd" class="eye-btn">
                            <svg id="regEyeOpen" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="regEyeClosed" class="w-[18px] h-[18px] hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <div class="flex gap-1 mt-2">
                        @foreach(['bar1','bar2','bar3','bar4'] as $b)
                        <div class="flex-1 h-[3px] bg-slate-100 rounded-full overflow-hidden"><div id="{{ $b }}" class="str-bar w-0"></div></div>
                        @endforeach
                    </div>
                    <p id="strengthLabel" class="text-[.7rem] mt-1 text-slate-300 h-4"></p>
                </div>

                <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                    Create Account
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <div class="mt-4 pt-4 border-t border-slate-100">
                <button id="backToLoginBtn"
                        class="w-full flex items-center justify-center gap-2 text-slate-400 hover:text-red-700 text-sm font-semibold transition py-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Login
                </button>
            </div>
        </div>

    </div>{{-- /form-panel --}}

    {{-- ════════════════════════════════════════
         RIGHT: VISUAL PANEL (crimson)
    ════════════════════════════════════════ --}}
    <div class="visual-panel">
        <canvas id="vCanvas"></canvas>
        <div class="v-orb" style="width:260px;height:260px;background:#b91c1c;top:-80px;right:-40px;filter:blur(64px);opacity:.35;animation-duration:9s"></div>
        <div class="v-orb" style="width:200px;height:200px;background:#dc2626;bottom:-60px;left:-30px;filter:blur(60px);opacity:.28;animation-duration:12s;animation-delay:-5s"></div>
        <div class="v-orb" style="width:150px;height:150px;background:#fca5a5;top:40%;left:20%;filter:blur(70px);opacity:.12;animation-duration:15s;animation-delay:-9s"></div>
        <div class="v-grid"></div>
        <div class="v-vig"></div>

        {{-- Main visual content --}}
        <div class="v-content">
            {{-- Floating logo with rings --}}
            <div class="logo-glow-wrap mb-6" style="animation:floatY 5s ease-in-out infinite">
                <div class="logo-pulse-ring"></div>
                <div class="logo-pulse-ring logo-pulse-ring-2"></div>
                <div class="logo-pulse-ring-3 absolute inset-[-18px] border-2 border-red-300/20 rounded-full" style="animation:ringOut 3s ease-out infinite;animation-delay:2.4s"></div>
                <div class="w-28 h-28 bg-white/10 border-2 border-white/20 rounded-3xl flex items-center justify-center backdrop-blur-sm shadow-2xl">
                    <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="UM Clinic" class="w-20 h-20 object-contain drop-shadow-2xl">
                </div>
            </div>

            {{-- Headline --}}
            <h2 class="text-white font-black text-2xl leading-tight mb-1 text-center">
                Your Health,<br>Our Priority
            </h2>
            <p class="text-red-200/70 text-sm mb-6 text-center font-medium">
                University of Mindanao · Tagum City
            </p>

            {{-- Live status dot --}}
            <div class="flex items-center gap-2 mb-7 bg-white/10 backdrop-blur-sm border border-white/14 rounded-full px-4 py-2">
                <span class="v-dot"></span>
                <span class="text-white/85 text-xs font-semibold">Clinic Portal Active</span>
            </div>

            {{-- Feature items --}}
            <div class="w-full space-y-2.5 max-w-xs">
                @foreach([
                    ['📅','Book Appointments','Schedule clinic visits anytime'],
                    ['📋','Medical Records','Access your full health history'],
                    ['💊','Medicine Tracking','Track dispensed medications'],
                ] as [$ico,$title,$desc])
                <div class="v-feat">
                    <span class="text-xl shrink-0">{{ $ico }}</span>
                    <div>
                        <div class="text-white font-bold text-sm leading-tight">{{ $title }}</div>
                        <div class="text-red-200/65 text-xs mt-0.5">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Bottom marquee ticker --}}
        <div class="v-ticker absolute bottom-0 left-0 right-0">
            <div class="v-tick-track">
                @php
                $_ticks = ['Book appointments online','View your medical history','Get health & wellness tips','Clinic is open weekdays','Track your medications','Powered by UM Clinic Portal'];
                @endphp
                @for($i=0;$i<2;$i++)
                    @foreach($_ticks as $t)
                    <div class="v-tick-item">
                        <span class="v-tick-sep"></span>
                        {{ $t }}
                    </div>
                    @endforeach
                @endfor
            </div>
        </div>
    </div>{{-- /visual-panel --}}

</div>{{-- /login-card --}}

<script>
/* ─── VISUAL PANEL PARTICLES ────────────────────────────── */
(function(){
    const c=document.getElementById('vCanvas');if(!c)return;
    const p=c.parentElement,ctx=c.getContext('2d');let W,H,pts=[];
    function resize(){W=c.width=p.offsetWidth;H=c.height=p.offsetHeight}
    function Pt(){this.reset=function(){this.x=Math.random()*W;this.y=Math.random()*H;this.r=Math.random()*1.5+.3;this.vx=(Math.random()-.5)*.35;this.vy=(Math.random()-.5)*.25;this.a=Math.random()*.25+.06;this.life=0;this.max=Math.random()*300+150};this.reset()}
    for(let i=0;i<55;i++){const p=new Pt();p.life=Math.random()*p.max;pts.push(p)}
    function draw(){ctx.clearRect(0,0,W,H);for(const p of pts){p.life++;if(p.life>p.max)p.reset();const t=p.life/p.max,f=t<.15?t/.15:t>.8?(1-t)/.2:1;ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle=`rgba(255,255,255,${p.a*f})`;ctx.fill();p.x+=p.vx;p.y+=p.vy}requestAnimationFrame(draw)}
    resize();window.addEventListener('resize',resize);draw();
})();

/* ─── PANEL SWITCHING ───────────────────────────────────── */
(function(){
    const lp=document.getElementById('loginPanel');
    const rp=document.getElementById('registerPanel');
    function switchTo(hide,show){
        hide.classList.add('panel-out');
        setTimeout(()=>{hide.classList.add('hidden');hide.classList.remove('panel-out');show.classList.remove('hidden');show.classList.add('panel-in');setTimeout(()=>show.classList.remove('panel-in'),400)},210);
    }
    document.getElementById('showRegisterBtn').addEventListener('click',()=>switchTo(lp,rp));
    document.getElementById('backToLoginBtn').addEventListener('click',()=>switchTo(rp,lp));

    @if($errors->hasAny(['reg_id','reg_password','email','first_name','last_name']))
    lp.classList.add('hidden');rp.classList.remove('hidden');
    @endif

    function pwdToggle(inputId,openId,closedId,btnId){
        const inp=document.getElementById(inputId),openEl=document.getElementById(openId),closEl=document.getElementById(closedId),btn=document.getElementById(btnId);
        if(!btn||!inp)return;
        btn.addEventListener('click',()=>{const h=inp.type==='password';inp.type=h?'text':'password';openEl.classList.toggle('hidden',h);closEl.classList.toggle('hidden',!h)});
    }
    pwdToggle('loginPassword','eyeOpen','eyeClosed','toggleLoginPwd');
    pwdToggle('regPassword','regEyeOpen','regEyeClosed','toggleRegPwd');

    document.getElementById('loginForm').addEventListener('submit',()=>{
        const btn=document.getElementById('loginBtn'),txt=document.getElementById('loginBtnText'),arr=document.getElementById('loginArrow'),spin=document.getElementById('loginSpinner');
        btn.disabled=true;txt.textContent='Signing in…';arr.classList.add('hidden');spin.classList.remove('hidden');
    });

    document.querySelectorAll('.role-pill input[type=radio]').forEach(r=>{
        r.addEventListener('change',()=>{const u=document.getElementById('username');if(u){u.focus();u.select()}});
    });

    document.querySelectorAll('.btn-primary').forEach(btn=>{
        btn.addEventListener('click',function(e){
            const r=this.getBoundingClientRect(),size=Math.max(r.width,r.height),el=document.createElement('span');
            el.className='ripple';
            Object.assign(el.style,{width:size+'px',height:size+'px',left:(e.clientX-r.left-size/2)+'px',top:(e.clientY-r.top-size/2)+'px'});
            this.appendChild(el);setTimeout(()=>el.remove(),600);
        });
    });
})();

/* ─── PASSWORD STRENGTH ─────────────────────────────────── */
function checkStrength(val){
    let s=0;if(val.length>=8)s++;if(/[A-Z]/.test(val))s++;if(/[0-9]/.test(val))s++;if(/[\W_]/.test(val))s++;
    const C=['#ef4444','#f97316','#eab308','#22c55e'],L=['Weak','Fair','Good','Strong'];
    ['bar1','bar2','bar3','bar4'].forEach((id,i)=>{const b=document.getElementById(id);b.style.width=i<s?'100%':'0';b.style.background=i<s?C[s-1]:'transparent'});
    const lbl=document.getElementById('strengthLabel');
    if(!val.length){lbl.textContent='';return}
    lbl.textContent=L[s-1]||'Weak';lbl.style.color=C[s-1]||'#ef4444';
}
</script>
</body>
</html>
