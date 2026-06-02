@extends('layouts.portal')
@section('title','Health & Safety – UM Clinic')
@section('page_title','Health & Safety')

@section('styles')
<style>
/* ── ANIMATIONS ─────────────────────────────────────────── */
@keyframes fadeUp  { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeIn  { from { opacity:0; } to { opacity:1; } }
@keyframes pulse   { 0%,100%{transform:scale(1)} 50%{transform:scale(1.06)} }

.afu  { animation: fadeUp .5s cubic-bezier(.4,0,.2,1) both; }
.afi  { animation: fadeIn .6s ease both; }
.d1{animation-delay:.05s}.d2{animation-delay:.10s}.d3{animation-delay:.15s}
.d4{animation-delay:.20s}.d5{animation-delay:.25s}.d6{animation-delay:.32s}

/* ── REVEAL ON SCROLL ─────────────────────────────────── */
.reveal { opacity:0; transform:translateY(22px); transition: opacity .55s ease, transform .55s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }

/* ── CARDS ────────────────────────────────────────────── */
.hs-card {
    background: #fff; border-radius: 1.25rem;
    border: 1px solid #f1f5f9;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: transform .22s ease, box-shadow .22s ease;
    overflow: hidden;
}
.hs-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.09); }

/* ── TIP IMAGE CARDS ──────────────────────────────────── */
.tip-img-card {
    border-radius: 1.15rem; overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    transition: transform .22s ease, box-shadow .22s ease;
    background: #f8fafc;
}
.tip-img-card:hover { transform: scale(1.02); box-shadow: 0 8px 28px rgba(0,0,0,.14); }
.tip-img-card img  { width:100%; height:200px; object-fit:cover; display:block; }

/* ── CATEGORY CARDS ───────────────────────────────────── */
.cat-card {
    border-radius: 1.15rem; padding: 1.4rem;
    border: 1.5px solid transparent;
    transition: all .22s ease;
    cursor: default;
}
.cat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.09); }

/* ── ACCORDION ────────────────────────────────────────── */
.acc-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem; cursor: pointer;
    border-bottom: 1px solid transparent;
    transition: background .15s;
    user-select: none;
}
.acc-header:hover { background: #fdf2f2; }
.acc-header.open  { border-bottom-color: #f1f5f9; }
.acc-body { display: none; padding: 1rem 1.25rem 1.25rem; }
.acc-body.open { display: block; animation: fadeIn .25s ease; }
.acc-icon { transition: transform .25s ease; font-size: 1rem; color: #991b1b; }
.acc-icon.open { transform: rotate(45deg); }

/* ── EMERGENCY CARD ───────────────────────────────────── */
.emerg-card {
    background: linear-gradient(135deg, #7f1d1d, #991b1b);
    border-radius: 1.15rem; padding: 1.25rem;
    color: white;
    transition: transform .2s;
}
.emerg-card:hover { transform: translateY(-2px); }

/* ── TAB BUTTONS ──────────────────────────────────────── */
.tab-btn {
    padding: .5rem 1.1rem; border-radius: .7rem; font-size: .8rem; font-weight: 700;
    border: 1.5px solid #e2e8f0; background: #fff; color: #64748b;
    cursor: pointer; transition: all .18s; white-space: nowrap;
}
.tab-btn.active { background: #991b1b; border-color: #991b1b; color: #fff; box-shadow: 0 4px 12px rgba(153,27,27,.25); }
.tab-btn:hover:not(.active) { border-color: #991b1b; color: #991b1b; }

/* ── TIP LIST ITEMS ───────────────────────────────────── */
.tip-li {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .65rem 0; border-bottom: 1px solid #f8fafc;
}
.tip-li:last-child { border: none; padding-bottom: 0; }
.tip-check {
    width: 1.5rem; height: 1.5rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .75rem; flex-shrink: 0; margin-top: .05rem;
}

/* ── STAT CHIPS ───────────────────────────────────────── */
.stat-chip {
    text-align: center; padding: .9rem .5rem;
    border-radius: 1rem; background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.15);
}
</style>
@endsection

@section('content')

{{-- ── HERO BANNER ── --}}
<div class="relative bg-gradient-to-br from-red-950 via-red-900 to-red-800 rounded-2xl overflow-hidden shadow-xl afu">
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,rgba(255,255,255,.07),transparent_60%)] pointer-events-none"></div>
    <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute top-4 right-6">
        <span class="text-6xl opacity-20 select-none">🏥</span>
    </div>
    <div class="relative z-10 px-6 py-7 sm:px-8">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 bg-white/12 border border-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-3">
                    💊 UM Clinic Resource Center
                </div>
                <h2 class="text-white font-black text-2xl sm:text-3xl leading-tight mb-1">Health &amp; Safety Guide</h2>
                <p class="text-red-300/80 text-sm">Your complete wellness reference at UM Tagum City</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 mt-5 max-w-sm">
            <div class="stat-chip">
                <div class="text-white font-black text-lg">24/7</div>
                <div class="text-red-300/75 text-[.62rem] font-semibold mt-0.5">Emergency</div>
            </div>
            <div class="stat-chip">
                <div class="text-white font-black text-lg">FREE</div>
                <div class="text-red-300/75 text-[.62rem] font-semibold mt-0.5">Consultation</div>
            </div>
            <div class="stat-chip">
                <div class="text-white font-black text-lg">4+</div>
                <div class="text-red-300/75 text-[.62rem] font-semibold mt-0.5">Categories</div>
            </div>
        </div>
    </div>
</div>

{{-- ── EMERGENCY CONTACTS ── --}}
<div class="reveal">
    <h3 class="text-slate-800 font-black text-base mb-3 flex items-center gap-2">
        <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center text-sm">🚨</span>
        Emergency Contacts
    </h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="emerg-card">
            <div class="text-2xl mb-2">🏥</div>
            <div class="font-black text-white text-sm mb-0.5">UM Clinic – Tagum</div>
            <div class="text-white/70 text-xs mb-2">Campus Health Services</div>
            <div class="text-amber-300 font-bold text-sm">+63 (084) 400 0000</div>
        </div>
        <div class="emerg-card" style="background:linear-gradient(135deg,#1e3a8a,#1d4ed8)">
            <div class="text-2xl mb-2">🚑</div>
            <div class="font-black text-white text-sm mb-0.5">Emergency Hotline</div>
            <div class="text-white/70 text-xs mb-2">Philippine Red Cross – Tagum</div>
            <div class="text-amber-300 font-bold text-sm">143 / 911</div>
        </div>
        <div class="emerg-card" style="background:linear-gradient(135deg,#065f46,#059669)">
            <div class="text-2xl mb-2">🏨</div>
            <div class="font-black text-white text-sm mb-0.5">Davao del Norte Hospital</div>
            <div class="text-white/70 text-xs mb-2">Nearest Referral Hospital</div>
            <div class="text-amber-300 font-bold text-sm">+63 (084) 218 8888</div>
        </div>
    </div>
</div>

{{-- ── CLINIC SCHEDULE ── --}}
<div class="hs-card reveal">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100">
        <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center text-base">📋</div>
        <div>
            <h3 class="font-black text-slate-800 text-sm">Clinic Information</h3>
            <p class="text-xs text-slate-400">Walk-in and appointment schedules</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
        @foreach([
            ['🕗','Clinic Hours','Monday – Friday: 8:00 AM – 5:00 PM','Saturday: 8:00 AM – 12:00 PM'],
            ['📍','Location','UM Tagum City Campus','Administration Building, Ground Floor'],
            ['✅','Services','Free consultation & first aid','Referral letters & health certificates'],
        ] as [$icon, $title, $line1, $line2])
        <div class="px-5 py-4 flex items-start gap-3">
            <div class="w-9 h-9 bg-slate-100 rounded-xl flex items-center justify-center text-base shrink-0">{{ $icon }}</div>
            <div>
                <div class="font-bold text-slate-800 text-sm">{{ $title }}</div>
                <div class="text-xs text-slate-500 mt-0.5">{{ $line1 }}</div>
                <div class="text-xs text-slate-400">{{ $line2 }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── TIP IMAGES (if available) ── --}}
@php $validImages = array_filter($tipImages, fn($img) => !empty($img)); @endphp
@if(!empty($validImages))
<div class="reveal">
    <h3 class="text-slate-800 font-black text-base mb-3 flex items-center gap-2">
        <span class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center text-sm">🖼️</span>
        Health Tip Posters
    </h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($validImages as $img)
        <div class="tip-img-card">
            <img src="{{ $img }}" alt="Health Tip"
                 onerror="this.closest('.tip-img-card').style.display='none'">
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── HEALTH CATEGORY TABS ── --}}
<div class="hs-card reveal">
    <div class="px-5 pt-5 pb-3 border-b border-slate-100">
        <h3 class="font-black text-slate-800 text-sm flex items-center gap-2 mb-3">
            <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center text-sm">💡</span>
            Wellness Tips by Category
        </h3>
        <div class="flex flex-wrap gap-2" id="tabBtns">
            <button class="tab-btn active" onclick="showTab('nutrition',this)">🥗 Nutrition</button>
            <button class="tab-btn" onclick="showTab('exercise',this)">🏃 Exercise</button>
            <button class="tab-btn" onclick="showTab('mental',this)">🧠 Mental Health</button>
            <button class="tab-btn" onclick="showTab('hygiene',this)">🧼 Hygiene</button>
            <button class="tab-btn" onclick="showTab('sleep',this)">😴 Sleep</button>
        </div>
    </div>

    {{-- Nutrition --}}
    <div id="tab-nutrition" class="tab-pane p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="cat-card" style="background:#fef9f0;border-color:#fde68a;">
                <div class="text-2xl mb-2">🥗</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Balanced Diet</h4>
                @foreach(['Fill half your plate with fruits and vegetables daily','Choose whole grains over refined carbohydrates','Limit processed foods, fast food, and sugary drinks','Include lean proteins — fish, chicken, beans, eggs'] as $t)
                <div class="tip-li"><div class="tip-check bg-amber-100 text-amber-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
            <div class="cat-card" style="background:#f0fdf4;border-color:#bbf7d0;">
                <div class="text-2xl mb-2">💧</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Hydration</h4>
                @foreach(['Drink at least 8 glasses (2 liters) of water daily','Increase intake during hot weather and exercise','Limit caffeinated drinks — coffee, energy drinks','Eat water-rich foods like cucumber, watermelon'] as $t)
                <div class="tip-li"><div class="tip-check bg-green-100 text-green-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Exercise --}}
    <div id="tab-exercise" class="tab-pane p-5 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="cat-card" style="background:#eff6ff;border-color:#bfdbfe;">
                <div class="text-2xl mb-2">🏃</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Daily Activity</h4>
                @foreach(['Aim for at least 30 minutes of moderate activity daily','Walking, jogging, cycling, and swimming are ideal','Take the stairs instead of the elevator when possible','Stretch for 10 minutes every morning to reduce stiffness'] as $t)
                <div class="tip-li"><div class="tip-check bg-blue-100 text-blue-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
            <div class="cat-card" style="background:#fdf4ff;border-color:#e9d5ff;">
                <div class="text-2xl mb-2">💪</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Safety During Exercise</h4>
                @foreach(['Always warm up before and cool down after exercise','Stay hydrated — drink water before, during, and after','Wear proper footwear and comfortable clothing','Stop immediately if you feel chest pain or dizziness'] as $t)
                <div class="tip-li"><div class="tip-check bg-purple-100 text-purple-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Mental Health --}}
    <div id="tab-mental" class="tab-pane p-5 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="cat-card" style="background:#fff7ed;border-color:#fed7aa;">
                <div class="text-2xl mb-2">🧠</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Stress Management</h4>
                @foreach(['Practice deep breathing: inhale 4s, hold 4s, exhale 6s','Take 5-minute breaks every hour during study sessions','Talk to a trusted friend, family member, or counselor','Engage in hobbies and activities you genuinely enjoy'] as $t)
                <div class="tip-li"><div class="tip-check bg-orange-100 text-orange-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
            <div class="cat-card" style="background:#f0fdf4;border-color:#bbf7d0;">
                <div class="text-2xl mb-2">🧘</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Emotional Wellness</h4>
                @foreach(['Set realistic academic and personal goals','Maintain a routine — regular sleep, meals, and exercise','Limit social media use to less than 2 hours per day','Seek clinic help if you feel overwhelmed or depressed'] as $t)
                <div class="tip-li"><div class="tip-check bg-green-100 text-green-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Hygiene --}}
    <div id="tab-hygiene" class="tab-pane p-5 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="cat-card" style="background:#f0fdfa;border-color:#99f6e4;">
                <div class="text-2xl mb-2">🧼</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Hand Hygiene</h4>
                @foreach(['Wash hands for at least 20 seconds with soap and water','Wash before eating, after using the restroom, and after coughing','Use alcohol-based sanitizer when soap is unavailable','Avoid touching your eyes, nose, and mouth with unwashed hands'] as $t)
                <div class="tip-li"><div class="tip-check bg-teal-100 text-teal-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
            <div class="cat-card" style="background:#fef2f2;border-color:#fecaca;">
                <div class="text-2xl mb-2">🦠</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Preventing Illness Spread</h4>
                @foreach(['Cover your mouth and nose with your elbow when coughing or sneezing','Wear a mask when you feel unwell or in crowded spaces','Avoid sharing personal items like utensils, towels, or earphones','Stay home and visit the clinic if you have a fever or flu symptoms'] as $t)
                <div class="tip-li"><div class="tip-check bg-red-100 text-red-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sleep --}}
    <div id="tab-sleep" class="tab-pane p-5 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="cat-card" style="background:#f5f3ff;border-color:#ddd6fe;">
                <div class="text-2xl mb-2">😴</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Sleep Quality</h4>
                @foreach(['Aim for 7–9 hours of sleep every night','Keep a consistent sleep and wake time, even on weekends','Avoid screens (phone, laptop) at least 30 minutes before bed','Keep your bedroom cool, dark, and quiet for better sleep'] as $t)
                <div class="tip-li"><div class="tip-check bg-violet-100 text-violet-700">✓</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
            <div class="cat-card" style="background:#fef9f0;border-color:#fde68a;">
                <div class="text-2xl mb-2">⚠️</div>
                <h4 class="font-black text-slate-800 text-sm mb-2">Signs of Sleep Deprivation</h4>
                @foreach(['Difficulty concentrating or remembering information','Frequent mood swings, irritability, or anxiety','Increased appetite and cravings for sugary foods','Visit the clinic if poor sleep persists beyond 2 weeks'] as $t)
                <div class="tip-li"><div class="tip-check bg-amber-100 text-amber-700">!</div><span class="text-xs text-slate-600">{{ $t }}</span></div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── FIRST AID BASICS ACCORDION ── --}}
<div class="hs-card reveal">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
            <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center text-sm">🩹</span>
            First Aid Quick Reference
        </h3>
        <p class="text-xs text-slate-400 mt-0.5 ml-9">Tap any item to expand</p>
    </div>

    @php
    $firstAid = [
        ['🤒', 'Fever (38°C and above)', 'red',
         ['Rest in a cool, well-ventilated room','Take paracetamol as directed on the packaging','Apply a cool, damp cloth on the forehead','Drink plenty of fluids — water, juice, or broth','Go to the clinic if fever exceeds 39°C or lasts more than 2 days']],
        ['🩸', 'Minor Cuts & Wounds', 'rose',
         ['Wash hands before treating any wound','Rinse wound gently with clean running water for 5 minutes','Apply gentle pressure with a clean cloth to stop bleeding','Cover with a sterile bandage or gauze','Visit the clinic if the wound is deep, jagged, or does not stop bleeding']],
        ['🤕', 'Headache / Migraine', 'amber',
         ['Rest in a quiet, dark room','Apply a cold or warm compress to forehead or neck','Stay hydrated — dehydration is a common cause','Avoid loud noises and bright screens','Seek clinic help if headache is sudden, severe, or recurring']],
        ['🤢', 'Stomach Ache / Vomiting', 'orange',
         ['Avoid solid food temporarily — start with clear fluids','Sip water or oral rehydration solution in small amounts','Rest and avoid sudden movement','Avoid dairy, fatty, or spicy foods until fully recovered','Visit the clinic if vomiting contains blood or lasts more than 24 hours']],
        ['🦟', 'Insect Bites / Allergic Reactions', 'green',
         ['Clean the bite area with soap and water','Apply a cold compress to reduce swelling and itching','Avoid scratching — it can cause infection','Take an antihistamine if available and itching is severe','Go to the clinic immediately if you develop difficulty breathing or severe swelling']],
        ['🔥', 'Minor Burns', 'blue',
         ['Cool the burn immediately under cool (not cold) running water for 10–20 minutes','Do not apply ice, butter, toothpaste, or any cream','Cover loosely with a sterile non-stick bandage','Do NOT break any blisters that form','Go to the clinic or emergency room for burns larger than your palm or on the face/hands']],
    ];
    @endphp

    @foreach($firstAid as $i => [$icon, $title, $color, $steps])
    <div class="border-b border-slate-100 last:border-0">
        <div class="acc-header" onclick="toggleAcc({{ $i }})">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-{{ $color }}-100 rounded-xl flex items-center justify-center text-base shrink-0">{{ $icon }}</div>
                <span class="font-bold text-slate-800 text-sm">{{ $title }}</span>
            </div>
            <span class="acc-icon" id="acc-icon-{{ $i }}">+</span>
        </div>
        <div class="acc-body" id="acc-body-{{ $i }}">
            <ol class="space-y-2 ml-12">
                @foreach($steps as $si => $step)
                <li class="flex items-start gap-2 text-sm text-slate-600">
                    <span class="w-5 h-5 bg-red-700 text-white text-[.65rem] font-black rounded-full flex items-center justify-center shrink-0 mt-0.5">{{ $si + 1 }}</span>
                    {{ $step }}
                </li>
                @endforeach
            </ol>
            <div class="mt-3 ml-12 inline-flex items-center gap-1.5 bg-red-50 border border-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                🏥 When in doubt, visit the UM Clinic immediately
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ── SEASONAL HEALTH ALERTS ── --}}
@php
$month = (int) now()->format('n');
$season = match(true) {
    in_array($month,[3,4,5])        => ['Summer / Dry Season','☀️','bg-amber-50 border-amber-200','text-amber-700','bg-amber-100','text-amber-800',
        ['Drink extra water — heat causes faster dehydration','Avoid prolonged outdoor exposure between 10 AM and 3 PM','Wear light-colored, breathable clothing','Watch for signs of heat stroke: dizziness, confusion, no sweating','Apply sunscreen (SPF 30+) before going outdoors']],
    in_array($month,[6,7,8,9,10])   => ['Rainy / Typhoon Season','🌧️','bg-blue-50 border-blue-200','text-blue-700','bg-blue-100','text-blue-800',
        ['Avoid wading through floodwaters — risk of leptospirosis','Boil or use bottled water after flooding','Watch for dengue symptoms: high fever, rash, body pain','Cover water containers to prevent mosquito breeding','Visit the clinic immediately if fever persists for 2+ days']],
    in_array($month,[11,12])        => ['"Ber" Months / Holiday Season','🎄','bg-green-50 border-green-200','text-green-700','bg-green-100','text-green-800',
        ['Get your annual flu vaccine if you have not yet','Wash hands frequently in crowded holiday gatherings','Avoid close contact with anyone showing flu symptoms','Maintain a balanced diet amid holiday celebrations','Get enough rest — do not let celebrations disrupt your sleep']],
    default                          => ['New Year / Cool Season','🎆','bg-indigo-50 border-indigo-200','text-indigo-700','bg-indigo-100','text-indigo-800',
        ['Dress in layers to handle changing temperatures','Keep wounds from firecrackers clean and covered','Schedule your annual health check-up early in the year','Stay hydrated even in cool weather — thirst sensation decreases','Visit the clinic for any firecracker-related injury immediately']],
};
@endphp

<div class="hs-card reveal">
    <div class="{{ $season[2] }} border px-5 py-4 flex items-center gap-3">
        <div class="w-10 h-10 {{ $season[4] }} rounded-xl flex items-center justify-center text-xl shrink-0">{{ $season[1] }}</div>
        <div>
            <h3 class="font-black text-sm {{ $season[5] }}">{{ $season[0] }} Alert</h3>
            <p class="text-xs {{ $season[3] }} opacity-75">Current health precautions for this season</p>
        </div>
        <span class="ml-auto text-xs {{ $season[3] }} font-bold bg-white/60 px-2.5 py-1 rounded-full border border-current/20">{{ now()->format('F Y') }}</span>
    </div>
    <div class="p-5">
        <ul class="space-y-2.5">
            @foreach($season[6] as $tip)
            <li class="flex items-start gap-3">
                <span class="w-5 h-5 {{ $season[4] }} {{ $season[3] }} text-[.7rem] font-black rounded-full flex items-center justify-center shrink-0 mt-0.5">✓</span>
                <span class="text-sm text-slate-600">{{ $tip }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- ── WHEN TO VISIT THE CLINIC ── --}}
<div class="hs-card reveal">
    <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-black text-slate-800 text-sm flex items-center gap-2">
            <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center text-sm">⚠️</span>
            When to Visit the Clinic
        </h3>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <h4 class="font-black text-red-800 text-sm mb-3 flex items-center gap-1.5">🚨 Visit Immediately</h4>
            @foreach(['Fever 39°C or higher','Severe chest pain or difficulty breathing','Uncontrolled bleeding','Suspected fracture or severe injury','Suspected allergic reaction','Loss of consciousness or severe dizziness'] as $s)
            <div class="flex items-start gap-2 mb-1.5"><span class="text-red-600 font-bold text-xs mt-0.5">→</span><span class="text-xs text-red-700">{{ $s }}</span></div>
            @endforeach
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <h4 class="font-black text-amber-800 text-sm mb-3 flex items-center gap-1.5">⏰ Schedule an Appointment</h4>
            @foreach(['Persistent cough or cold for 5+ days','Recurring headaches or migraines','Digestive issues lasting more than 3 days','Skin rashes or allergic conditions','Need for a health or medical certificate','General check-up and wellness consultation'] as $s)
            <div class="flex items-start gap-2 mb-1.5"><span class="text-amber-600 font-bold text-xs mt-0.5">→</span><span class="text-xs text-amber-700">{{ $s }}</span></div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* ── TAB SYSTEM ───────────────────────────────────────── */
function showTab(id, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.remove('hidden');
    btn.classList.add('active');
}

/* ── ACCORDION ────────────────────────────────────────── */
function toggleAcc(i) {
    const body = document.getElementById('acc-body-' + i);
    const icon = document.getElementById('acc-icon-' + i);
    const header = body.previousElementSibling;
    const isOpen = body.classList.toggle('open');
    icon.classList.toggle('open', isOpen);
    header.classList.toggle('open', isOpen);
}

/* ── SCROLL REVEAL ────────────────────────────────────── */
(function () {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); } });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
})();
</script>
@endsection
