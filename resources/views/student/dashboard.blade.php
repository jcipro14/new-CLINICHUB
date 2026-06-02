@extends('layouts.portal')
@section('title','Dashboard – UM Clinic')
@section('page_title','Student Dashboard')

@section('styles')
<style>
/*
 * STUDENT DASHBOARD — TOTAL REDESIGN
 * Layout: Cinematic hero → Marquee → Color stats → Bento grid
 * Palette: Crimson primary · Vivid card accents · Deep navy feature zones
 */

/* ── KEYFRAMES ─────────────────────────────────────────────── */
@keyframes charUp    {from{transform:translateY(110%);opacity:0}to{transform:translateY(0);opacity:1}}
@keyframes fadeUp    {from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes scaleIn   {from{opacity:0;transform:scale(.86)}to{opacity:1;transform:scale(1)}}
@keyframes blob      {
  0%,100%{border-radius:62% 38% 34% 66%/56% 33% 67% 44%;transform:scale(1)rotate(0deg)}
  35%    {border-radius:30% 70% 68% 32%/47% 64% 36% 53%;transform:scale(1.07)rotate(5deg)}
  68%    {border-radius:72% 28% 47% 53%/29% 62% 38% 71%;transform:scale(.93)rotate(-4deg)}
}
@keyframes gradShift {0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
@keyframes pulseDot  {0%,100%{box-shadow:0 0 0 0 currentColor;opacity:1}60%{box-shadow:0 0 0 7px transparent;opacity:.6}}
@keyframes ringOut   {0%{transform:scale(1);opacity:.5}100%{transform:scale(2.8);opacity:0}}
@keyframes floatY    {0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
@keyframes marqL     {from{transform:translateX(0)}to{transform:translateX(-50%)}}
@keyframes shimBar   {from{background-position:-200% 0}to{background-position:200% 0}}
@keyframes spinFloat {0%,100%{transform:translateY(0)rotate(0deg)}50%{transform:translateY(-9px)rotate(9deg)}}
@keyframes glowB     {0%,100%{box-shadow:0 0 0 1px rgba(255,255,255,.12)}50%{box-shadow:0 0 0 2px rgba(255,255,255,.28)}}
@keyframes countPop  {0%{transform:translateY(22px);opacity:0}65%{transform:translateY(-3px)}100%{transform:translateY(0);opacity:1}}
@keyframes orbGlow   {0%,100%{filter:blur(62px);opacity:.22}50%{filter:blur(74px);opacity:.32}}
@keyframes barFill   {from{width:0}to{width:var(--w)}}
@keyframes slideR    {from{opacity:0;transform:translateX(-10px)}to{opacity:1;transform:translateX(0)}}
@keyframes popIn     {0%{opacity:0;transform:scale(.7)}70%{transform:scale(1.06)}100%{opacity:1;transform:scale(1)}}

/* ── ENTRANCE ─────────────────────────────────────────────── */
.afu{animation:fadeUp .7s cubic-bezier(.22,1,.36,1) both}
.asc{animation:scaleIn .55s cubic-bezier(.22,1,.36,1) both}
.d0{animation-delay:0s}.d1{animation-delay:.06s}.d2{animation-delay:.12s}
.d3{animation-delay:.18s}.d4{animation-delay:.25s}.d5{animation-delay:.32s}
.d6{animation-delay:.39s}.d7{animation-delay:.46s}.d8{animation-delay:.53s}

/* ── REVEAL ──────────────────────────────────────────────── */
.reveal{opacity:0;transform:translateY(24px);transition:opacity .7s cubic-bezier(.22,1,.36,1),transform .7s cubic-bezier(.22,1,.36,1)}
.reveal.visible{opacity:1;transform:translateY(0)}

/* ──────────────────────────────────────────────────────────
   CURSOR GLOW
──────────────────────────────────────────────────────────── */
#cursorGlow{
  position:fixed;width:520px;height:520px;border-radius:50%;
  background:radial-gradient(circle,rgba(185,28,28,.08) 0%,transparent 65%);
  pointer-events:none;z-index:9990;transform:translate(-50%,-50%);
  will-change:left,top;
}

/* ──────────────────────────────────────────────────────────
   HERO
──────────────────────────────────────────────────────────── */
.hero-shell{border-radius:2rem;overflow:hidden;position:relative;isolation:isolate;min-height:210px}
#heroCanvas{position:absolute;inset:0;z-index:1;pointer-events:none}
.hero-orb{position:absolute;border-radius:50%;pointer-events:none;z-index:2;animation:blob linear infinite,orbGlow 5s ease-in-out infinite}
.hero-grid{
  position:absolute;inset:0;z-index:3;pointer-events:none;
  background-image:linear-gradient(rgba(255,255,255,.044) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.044) 1px,transparent 1px);
  background-size:54px 54px;
  mask-image:radial-gradient(ellipse 100% 100% at center,black 10%,transparent 72%);
}
.hero-vig{position:absolute;inset:0;z-index:4;pointer-events:none;background:linear-gradient(to right,rgba(0,0,0,.42) 0%,transparent 55%),radial-gradient(ellipse 80% 55% at 50% 110%,rgba(0,0,0,.55),transparent 70%)}
.hero-body{position:relative;z-index:5;padding:2.5rem 2.3rem 2.3rem}
.c-clip{overflow:hidden;display:inline-block;vertical-align:bottom;line-height:1.1}
.c-char{display:inline-block;animation:charUp .58s cubic-bezier(.22,1,.36,1) both}
.s-pill{display:inline-flex;align-items:center;gap:.5rem;backdrop-filter:blur(18px);-webkit-backdrop-filter:blur(18px);border-radius:999px;padding:.3rem .88rem;font-size:.72rem;font-weight:700;letter-spacing:.025em;border:1.5px solid;animation:glowB 3s ease infinite;transition:transform .2s}
.s-dot-wrap{position:relative;display:inline-flex;align-items:center;justify-content:center;width:13px;height:13px}
.s-dot{width:8px;height:8px;border-radius:50%;animation:pulseDot 2.5s ease-in-out infinite;position:relative;z-index:2}
.s-ring{position:absolute;inset:-3px;border-radius:50%;border:1.5px solid currentColor;animation:ringOut 2.5s ease-out infinite;z-index:1}
.s-ring-2{animation-delay:.9s}

/* ──────────────────────────────────────────────────────────
   WELLNESS MARQUEE  (at top now)
──────────────────────────────────────────────────────────── */
.marq-shell{
  border-radius:1.4rem;overflow:hidden;position:relative;
  background:linear-gradient(135deg,#0f766e,#0d9488,#14b8a6);
  padding:.85rem 0;
}
.marq-shell::before,.marq-shell::after{content:'';position:absolute;top:0;bottom:0;width:80px;z-index:2;pointer-events:none}
.marq-shell::before{left:0;background:linear-gradient(90deg,#0f766e 10%,transparent 100%)}
.marq-shell::after {right:0;background:linear-gradient(-90deg,#14b8a6 10%,transparent 100%)}
.marq-track{display:flex;width:max-content;user-select:none;animation:marqL 44s linear infinite}
.marq-track:hover{animation-play-state:paused}
.m-item{display:inline-flex;align-items:center;gap:.55rem;padding:0 2.4rem;white-space:nowrap;font-size:.79rem;color:rgba(255,255,255,.92);font-weight:600}
.m-sep{width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.5);flex-shrink:0}

/* ──────────────────────────────────────────────────────────
   STAT CARDS — reference tall-portrait style
──────────────────────────────────────────────────────────── */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem}
@media(max-width:1023px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:540px) {.stat-grid{grid-template-columns:1fr}}

/* ── Base card ── */
.scard{
  position:relative;overflow:hidden;border-radius:1.5rem;
  padding:1.75rem 1.6rem 1.5rem;
  min-height:400px;
  display:flex;flex-direction:column;
  transition:transform .4s cubic-bezier(.22,1,.36,1),box-shadow .4s ease;
  will-change:transform;cursor:default;
}
.scard:hover{transform:translateY(-10px) scale(1.015);box-shadow:0 36px 64px -10px var(--sh,rgba(0,0,0,.4))}

/* Dot-grid texture (key reference detail) */
.scard::before{
  content:'';position:absolute;inset:0;pointer-events:none;z-index:0;
  background-image:radial-gradient(circle,rgba(255,255,255,.13) 1px,transparent 1px);
  background-size:22px 22px;
}
/* Large radial glow blob */
.scard::after{
  content:'';position:absolute;top:-40px;right:-40px;
  width:180px;height:180px;border-radius:50%;
  background:rgba(255,255,255,.12);
  filter:blur(32px);pointer-events:none;z-index:0;
}
.scard>*{position:relative;z-index:1}

/* ── Typography ── */
.sc-label{
  font-size:.6rem;font-weight:800;text-transform:uppercase;
  letter-spacing:.14em;color:rgba(255,255,255,.6);
  margin-bottom:.7rem;
}
.sc-num{
  display:block;
  font-size:clamp(3.4rem,5vw,5rem);
  font-weight:900;line-height:1;letter-spacing:-.04em;
  color:#fff;font-variant-numeric:tabular-nums;
  margin-bottom:.65rem;
  animation:countPop .65s cubic-bezier(.22,1,.36,1) both;
}
.sc-desc{
  font-size:.78rem;line-height:1.6;
  color:rgba(255,255,255,.68);
  flex:1;margin-bottom:1rem;
}

/* ── Mini bar chart ── */
.sc-chart{margin-bottom:1.2rem}
.sc-bars{
  display:flex;align-items:flex-end;gap:.4rem;height:80px;
  padding-bottom:1.4rem;/* space for labels */
  position:relative;
}
.sc-bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:.3rem;height:100%}
.sc-bar{
  width:100%;border-radius:.35rem .35rem 0 0;
  background:rgba(255,255,255,.22);
  transition:height 1.5s cubic-bezier(.22,1,.36,1);
  height:0;position:relative;
  min-height:0;
}
.sc-bar.hi{background:rgba(255,255,255,.58)}
.sc-bar-v{
  position:absolute;top:-.05rem;left:50%;transform:translate(-50%,-100%);
  font-size:.6rem;font-weight:800;color:rgba(255,255,255,.9);
  white-space:nowrap;
}
.sc-bar-l{
  font-size:.55rem;font-weight:700;color:rgba(255,255,255,.5);
  white-space:nowrap;text-align:center;
  position:absolute;bottom:0;width:100%;
}

/* ── Progress bar ── */
.sc-prog{margin-bottom:1.2rem}
.sc-prog-info{display:flex;justify-content:space-between;margin-bottom:.5rem}
.sc-prog-lbl{font-size:.68rem;font-weight:700;color:rgba(255,255,255,.65)}
.sc-prog-pct{font-size:.68rem;font-weight:800;color:#fff}
.sc-prog-track{height:9px;background:rgba(255,255,255,.18);border-radius:6px;overflow:hidden}
.sc-prog-fill{height:9px;background:#fff;border-radius:6px;width:0;transition:width 1.8s cubic-bezier(.22,1,.36,1)}
/* secondary progress fill (colored) */
.sc-prog-fill.amber{background:rgba(255,255,255,.9)}
.sc-prog-fill.violet{background:rgba(255,255,255,.9)}

/* ── Footer ── */
.sc-foot{margin-top:auto}
.sc-divider{border:none;border-top:1px solid rgba(255,255,255,.2);margin-bottom:.9rem}
.sc-cta{
  display:flex;align-items:center;justify-content:space-between;
  color:#fff;font-weight:700;font-size:.88rem;
  text-decoration:none;
  transition:opacity .2s;
}
.sc-cta:hover{opacity:.75}
.sc-arrow{
  width:2.1rem;height:2.1rem;border-radius:50%;
  border:1.5px solid rgba(255,255,255,.38);
  display:flex;align-items:center;justify-content:center;
  font-size:.9rem;flex-shrink:0;
  transition:background .2s,border-color .2s,transform .2s;
}
.sc-cta:hover .sc-arrow{background:rgba(255,255,255,.18);border-color:rgba(255,255,255,.65);transform:translateX(3px)}

/* keep old aliases used in immersive override */
.snum{font-size:clamp(3.4rem,5vw,5rem)}

/* ──────────────────────────────────────────────────────────
   QUICK ACCESS
──────────────────────────────────────────────────────────── */
.quick-wrap{background:#fff;border-radius:1.4rem;border:1px solid #f0f0f4;overflow:hidden}
.quick-head{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.3rem;border-bottom:1px solid #f6f6f8;background:linear-gradient(135deg,#fafafa,#f8f8fb)}
.quick-pills{display:flex;gap:.65rem;overflow-x:auto;padding:.1rem 0;scrollbar-width:none}
.quick-pills::-webkit-scrollbar{display:none}
.q-pill{
  display:inline-flex;align-items:center;gap:.55rem;white-space:nowrap;
  padding:.6rem 1.1rem;border-radius:999px;
  border:1.5px solid #ebebef;background:#fafbfc;
  font-size:.78rem;font-weight:700;color:#374151;
  text-decoration:none;flex-shrink:0;
  transition:all .3s cubic-bezier(.22,1,.36,1);
  position:relative;overflow:hidden;
}
.q-pill::after{content:'';position:absolute;inset:0;border-radius:inherit;background:linear-gradient(135deg,#7f1d1d,#be123c);opacity:0;transition:opacity .3s}
.q-pill:hover::after{opacity:1}
.q-pill:hover{color:#fff;border-color:transparent;transform:translateY(-4px) scale(1.04);box-shadow:0 12px 26px rgba(127,29,29,.3)}
.q-pill:active{transform:scale(.97)}
.q-pill-ico{font-size:.95rem;transition:transform .3s cubic-bezier(.22,1,.36,1);position:relative;z-index:1}
.q-pill:hover .q-pill-ico{transform:scale(1.22) rotate(-6deg)}
.q-pill span{position:relative;z-index:1}

/* ──────────────────────────────────────────────────────────
   BENTO GRID CARDS
──────────────────────────────────────────────────────────── */
.b-card{border-radius:1.4rem;overflow:hidden;transition:transform .36s cubic-bezier(.22,1,.36,1),box-shadow .36s ease;will-change:transform}
.b-card:hover{transform:translateY(-5px);box-shadow:0 24px 52px -10px rgba(0,0,0,.13)}

/* Dark appointment card */
.appt-card{background:linear-gradient(145deg,#0f0e17,#1a1625,#110d1f);color:#fff}
.appt-card-has{background:linear-gradient(145deg,#0c1a2e,#0f2847,#0a1628)}

.appt-meta-row{display:flex;align-items:center;gap:.72rem;padding:.72rem;border-radius:.9rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.09);transition:background .2s,transform .22s cubic-bezier(.22,1,.36,1)}
.appt-meta-row:hover{background:rgba(255,255,255,.11);transform:translateX(5px)}
.appt-meta-ico{width:2.4rem;height:2.4rem;border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}

/* Records card */
.rec-card{background:#fff;border:1px solid #f0f0f4}
.rec-row{display:flex;align-items:center;gap:.75rem;padding:.78rem 1.25rem;transition:background .15s;cursor:default}
.rec-row:hover{background:#fafafa}

/* Health card */
.health-card{background:#fff;border:1px solid #f0f0f4}
.snap-item{padding:1rem 1.05rem;border-radius:1.05rem;position:relative;overflow:hidden;border:1px solid;transition:transform .25s cubic-bezier(.22,1,.36,1),box-shadow .25s}
.snap-item:hover{transform:translateY(-3px);box-shadow:0 10px 24px rgba(0,0,0,.1)}
.h-bar-track{height:7px;background:rgba(0,0,0,.06);border-radius:6px;overflow:hidden}
.h-bar-fill{height:7px;border-radius:6px;width:0;transition:width 1.9s cubic-bezier(.22,1,.36,1)}

/* card heads */
.c-head{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;border-bottom:1px solid rgba(255,255,255,.07)}
.c-head-light{display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.25rem;border-bottom:1px solid #f6f6f8;background:linear-gradient(135deg,#fafafa,#f8f8fb)}
.c-eyebrow{font-size:.61rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;margin-top:.15rem}

/* ──────────────────────────────────────────────────────────
   CONFIRM BANNER
──────────────────────────────────────────────────────────── */
.confirm-banner{background:linear-gradient(135deg,#fffbeb,#fef9e7);border:1.5px solid #fcd34d;border-radius:1.4rem;position:relative;overflow:hidden;animation:fadeUp .5s cubic-bezier(.22,1,.36,1) both}
.confirm-banner::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#f59e0b,#fbbf24,#f59e0b);background-size:200% 100%;animation:shimBar 2.5s linear infinite}

/* ──────────────────────────────────────────────────────────
   COUNTDOWN BADGE
──────────────────────────────────────────────────────────── */
.cd-badge{display:inline-flex;align-items:center;gap:.3rem;color:#fff;border-radius:999px;padding:.28rem .85rem;font-size:.68rem;font-weight:800;letter-spacing:.03em}
.cd-def  {background:linear-gradient(135deg,#991b1b,#b91c1c);box-shadow:0 4px 12px rgba(153,27,27,.44)}
.cd-today{background:linear-gradient(135deg,#059669,#10b981);box-shadow:0 4px 12px rgba(5,150,105,.38)}
.cd-pend {background:linear-gradient(135deg,#b45309,#d97706);box-shadow:0 4px 12px rgba(180,83,9,.38)}

/* ──────────────────────────────────────────────────────────
   BUTTONS
──────────────────────────────────────────────────────────── */
.btn-hero{display:inline-flex;align-items:center;gap:1.5rem;background:rgba(255,255,255,.1);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);color:rgba(255,255,255,.88);font-weight:700;font-size:.72rem;border-radius:999px;padding:.32rem .9rem;border:1.5px solid rgba(255,255,255,.16);transition:all .25s;animation:glowB 3s ease infinite}
.btn-hero:hover{background:rgba(255,255,255,.22);transform:translateY(-1px)}

.btn-book{display:inline-flex;align-items:center;gap:.45rem;background:linear-gradient(135deg,#7f1d1d,#be123c);color:#fff;font-weight:700;font-size:.82rem;border-radius:.95rem;padding:.72rem 1.4rem;box-shadow:0 5px 18px rgba(127,29,29,.38);transition:all .28s cubic-bezier(.22,1,.36,1);position:relative;overflow:hidden;cursor:pointer}
.btn-book::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(255,255,255,.16),transparent);opacity:0;transition:opacity .2s;border-radius:inherit}
.btn-book:hover::after{opacity:1}
.btn-book:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(127,29,29,.5)}
.btn-book:active{transform:scale(.97)}

.btn-outline-dark{display:flex;align-items:center;justify-content:center;gap:.4rem;width:100%;color:rgba(255,255,255,.6);font-weight:700;font-size:.78rem;padding:.65rem;border-radius:.9rem;background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.12);transition:all .25s cubic-bezier(.22,1,.36,1)}
.btn-outline-dark:hover{background:rgba(255,255,255,.14);color:#fff;border-color:rgba(255,255,255,.22);transform:translateY(-1px)}

.btn-outline-light{display:flex;align-items:center;justify-content:center;gap:.4rem;width:100%;color:#64748b;font-weight:700;font-size:.78rem;padding:.65rem;border-radius:.9rem;background:#f9f9fb;border:1.5px solid #ebebef;transition:all .25s cubic-bezier(.22,1,.36,1)}
.btn-outline-light:hover{background:#7f1d1d;color:#fff;border-color:#7f1d1d;box-shadow:0 8px 22px rgba(127,29,29,.3);transform:translateY(-1px)}

/* ──────────────────────────────────────────────────────────
   MISC
──────────────────────────────────────────────────────────── */
::-webkit-scrollbar{width:4px}
::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px}

/* ──────────────────────────────────────────────────────────
   HERO TYPOGRAPHY — theme-aware readable text
──────────────────────────────────────────────────────────── */
.hero-greeting-pill{
  display:inline-flex;align-items:center;gap:.4rem;
  border-radius:999px;padding:.28rem .9rem;
  font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.16em;
  border:1.5px solid;backdrop-filter:blur(10px);
  background:var(--gpill-bg);color:var(--gpill-color);border-color:var(--gpill-border);
}
.hero-clock-chip{
  display:inline-flex;align-items:center;gap:.55rem;
  background:rgba(0,0,0,.3);backdrop-filter:blur(14px);
  border:1px solid rgba(255,255,255,.16);border-radius:.75rem;
  padding:.3rem .8rem;
}
.hero-clock-chip .ck-time{color:var(--clock-color);font-family:ui-monospace,monospace;font-size:.74rem;font-weight:700;letter-spacing:.06em}
.hero-clock-chip .ck-sep{color:rgba(255,255,255,.22);font-size:.7rem}
.hero-clock-chip .ck-date{color:rgba(255,255,255,.65);font-size:.68rem;font-weight:500}
.hero-id-tag{
  display:inline-flex;align-items:center;
  background:var(--id-bg);color:var(--id-color);
  border:1.5px solid var(--id-border);
  border-radius:.55rem;padding:.18rem .65rem;
  font-size:.76rem;font-weight:800;letter-spacing:.04em;
}
.hero-course{font-size:.78rem;font-weight:600;color:var(--course-color)}
.hero-meta-sep{color:rgba(255,255,255,.28);font-size:.85rem;font-weight:300}
.h-stat-chip{
  display:inline-flex;align-items:center;gap:.35rem;
  background:rgba(255,255,255,.1);backdrop-filter:blur(8px);
  border:1px solid rgba(255,255,255,.16);border-radius:.65rem;
  padding:.25rem .7rem;
  transition:background .2s;
}
.h-stat-chip:hover{background:rgba(255,255,255,.18)}
.h-stat-num{color:var(--gpill-color);font-size:.8rem;font-weight:900;font-variant-numeric:tabular-nums}
.h-stat-lbl{color:rgba(255,255,255,.6);font-size:.67rem;font-weight:600}
.hero-stripe{
  position:absolute;bottom:0;left:0;right:0;height:3px;z-index:6;
  background:linear-gradient(90deg,transparent 0%,var(--stripe1) 25%,var(--stripe2) 75%,transparent 100%);
  opacity:.75;
}
.btn-hero{
  display:inline-flex;align-items:center;gap:.5rem;
  background:rgba(255,255,255,.12);backdrop-filter:blur(16px);
  color:rgba(255,255,255,.92);font-weight:700;font-size:.75rem;
  border-radius:999px;padding:.38rem 1.05rem;
  border:1.5px solid rgba(255,255,255,.22);
  transition:all .26s cubic-bezier(.22,1,.36,1);
}
.btn-hero:hover{background:rgba(255,255,255,.24);transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,.3)}

/* ══════════════════════════════════════════════════════════
   IMMERSIVE FULL-VIEW OVERRIDE  (student dashboard only)
   — overrides portal.blade.php defaults via !important
══════════════════════════════════════════════════════════ */

/* 1 ── Clean light background */
body{
  background: #eef0f7 !important;
  background-attachment:fixed !important;
}

/* 2 ── Topbar: crisp white, unchanged */
#portalTopbar{
  background:#fff !important;
  backdrop-filter:none !important;
  -webkit-backdrop-filter:none !important;
  border-color:#e2e8f0 !important;
  box-shadow:0 1px 4px rgba(0,0,0,.06) !important;
}

/* 3 ── Full-width content — strip max-width lock */
#portalMain>main{
  max-width:100% !important;
  padding-left:1.5rem !important;
  padding-right:1.5rem !important;
}

/* 4 ── Cards: soft elevated shadows on light bg */
.scard{
  box-shadow:0 12px 48px rgba(0,0,0,.18),0 2px 10px rgba(0,0,0,.1) !important;
}
.b-card,.d-card,.rec-card,.health-card,.quick-wrap{
  box-shadow:0 4px 24px rgba(0,0,0,.1),0 1px 4px rgba(0,0,0,.06) !important;
}
.marq-shell{
  box-shadow:0 4px 20px rgba(0,0,0,.1) !important;
}
.confirm-banner{
  box-shadow:0 4px 20px rgba(0,0,0,.1) !important;
}

/* 5 ── Flash success → light theme (unchanged) */
/* 6 ── Error banner → light theme (unchanged) */

/* 7 ── Scrollbar: light themed */
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:#f1f5f9}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}
::-webkit-scrollbar-thumb:hover{background:#94a3b8}

/* 8 ── Sidebar shadow on light bg */
#portalSidebar{box-shadow:4px 0 32px rgba(0,0,0,.18) !important}
</style>
@endsection

@section('content')

{{-- CURSOR GLOW --}}
<div id="cursorGlow"></div>

{{-- ── CONFIRMATION BANNER ── --}}
@if($pendingConfirmation)
<div class="confirm-banner p-4 flex flex-col sm:flex-row sm:items-center gap-4">
    <div class="flex items-start gap-3 flex-1">
        <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center text-xl shrink-0">📋</div>
        <div>
            <p class="font-bold text-amber-900 text-sm">Action Required — Appointment Confirmation</p>
            <p class="text-amber-700 text-xs mt-0.5">Scheduled for <strong>{{ $pendingConfirmation->next_consultation?->format('F j, Y') }}</strong>@if($pendingConfirmation->doctor) with <strong>Dr. {{ $pendingConfirmation->doctor }}</strong>@endif</p>
        </div>
    </div>
    <div class="flex gap-2 shrink-0">
        <button onclick="respondAppointment({{ $pendingConfirmation->appointment_id }},'accept',this)"
                class="flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm">✅ Accept</button>
        <button onclick="respondAppointment({{ $pendingConfirmation->appointment_id }},'cancel',this)"
                class="flex items-center gap-1.5 bg-red-600 hover:bg-red-700 active:scale-95 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm">❌ Cancel</button>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ --}}
@php
/* ══════════════════════════════════════════════════
   PER-THEME COLOR PALETTE — all text stays readable
   accent     : vivid highlight (greeting pill, stat nums, ID tag)
   accentMuted: semi-transparent accent (pill bg/border)
   clockColor : clock text color
   courseColor: course label color (contrast-friendly)
   stripe1/2  : bottom decorative stripe colors
══════════════════════════════════════════════════ */
$_theme = $settings->active_theme ?? 'default';

$_palette = match($_theme) {
    'summer'           => [
        'bg'          => 'linear-gradient(145deg,#150800,#2d1200,#180900)',
        'o1'          => '#ea580c', 'o2'          => '#f59e0b',
        'accent'      => '#fbbf24', 'accentMuted' => 'rgba(251,191,36,.18)',
        'accentBorder'=> 'rgba(251,191,36,.35)',
        'clockColor'  => '#fde68a', 'courseColor' => 'rgba(253,230,138,.82)',
        'stripe1'     => '#f59e0b', 'stripe2'     => '#fbbf24',
        'idBg'        => 'rgba(251,191,36,.15)', 'idBorder' => 'rgba(251,191,36,.4)',
    ],
    'christmas'        => [
        'bg'          => 'linear-gradient(145deg,#010d02,#021a06,#030f02)',
        'o1'          => '#15803d', 'o2'          => '#16a34a',
        'accent'      => '#86efac', 'accentMuted' => 'rgba(134,239,172,.18)',
        'accentBorder'=> 'rgba(134,239,172,.35)',
        'clockColor'  => '#bbf7d0', 'courseColor' => 'rgba(187,247,208,.82)',
        'stripe1'     => '#4ade80', 'stripe2'     => '#86efac',
        'idBg'        => 'rgba(134,239,172,.15)', 'idBorder' => 'rgba(134,239,172,.4)',
    ],
    'rainy_season'     => [
        'bg'          => 'linear-gradient(145deg,#010814,#021230,#010610)',
        'o1'          => '#2563eb', 'o2'          => '#3b82f6',
        'accent'      => '#93c5fd', 'accentMuted' => 'rgba(147,197,253,.18)',
        'accentBorder'=> 'rgba(147,197,253,.35)',
        'clockColor'  => '#bfdbfe', 'courseColor' => 'rgba(191,219,254,.82)',
        'stripe1'     => '#60a5fa', 'stripe2'     => '#93c5fd',
        'idBg'        => 'rgba(147,197,253,.15)', 'idBorder' => 'rgba(147,197,253,.4)',
    ],
    'holy_week'        => [
        'bg'          => 'linear-gradient(145deg,#07000e,#140030,#07000c)',
        'o1'          => '#7c3aed', 'o2'          => '#a855f7',
        'accent'      => '#d8b4fe', 'accentMuted' => 'rgba(216,180,254,.18)',
        'accentBorder'=> 'rgba(216,180,254,.35)',
        'clockColor'  => '#e9d5ff', 'courseColor' => 'rgba(233,213,255,.82)',
        'stripe1'     => '#c084fc', 'stripe2'     => '#d8b4fe',
        'idBg'        => 'rgba(216,180,254,.15)', 'idBorder' => 'rgba(216,180,254,.4)',
    ],
    'halloween'        => [
        'bg'          => 'linear-gradient(145deg,#080300,#1e0c00,#080300)',
        'o1'          => '#ea580c', 'o2'          => '#d97706',
        'accent'      => '#fdba74', 'accentMuted' => 'rgba(253,186,116,.18)',
        'accentBorder'=> 'rgba(253,186,116,.35)',
        'clockColor'  => '#fed7aa', 'courseColor' => 'rgba(254,215,170,.82)',
        'stripe1'     => '#fb923c', 'stripe2'     => '#fdba74',
        'idBg'        => 'rgba(253,186,116,.15)', 'idBorder' => 'rgba(253,186,116,.4)',
    ],
    'new_year'         => [
        'bg'          => 'linear-gradient(145deg,#02000e,#060030,#020008)',
        'o1'          => '#4f46e5', 'o2'          => '#6366f1',
        'accent'      => '#a5b4fc', 'accentMuted' => 'rgba(165,180,252,.18)',
        'accentBorder'=> 'rgba(165,180,252,.35)',
        'clockColor'  => '#e0e7ff', 'courseColor' => 'rgba(224,231,255,.82)',
        'stripe1'     => '#818cf8', 'stripe2'     => '#a5b4fc',
        'idBg'        => 'rgba(165,180,252,.15)', 'idBorder' => 'rgba(165,180,252,.4)',
    ],
    'independence_day' => [
        'bg'          => 'linear-gradient(145deg,#010a1e,#031848,#010818)',
        'o1'          => '#2563eb', 'o2'          => '#ef4444',
        'accent'      => '#7dd3fc', 'accentMuted' => 'rgba(125,211,252,.18)',
        'accentBorder'=> 'rgba(125,211,252,.35)',
        'clockColor'  => '#bae6fd', 'courseColor' => 'rgba(186,230,253,.82)',
        'stripe1'     => '#38bdf8', 'stripe2'     => '#7dd3fc',
        'idBg'        => 'rgba(125,211,252,.15)', 'idBorder' => 'rgba(125,211,252,.4)',
    ],
    'undas'            => [
        'bg'          => 'linear-gradient(145deg,#050302,#100e0a,#050302)',
        'o1'          => '#78716c', 'o2'          => '#57534e',
        'accent'      => '#d6d3d1', 'accentMuted' => 'rgba(214,211,209,.15)',
        'accentBorder'=> 'rgba(214,211,209,.3)',
        'clockColor'  => '#e7e5e4', 'courseColor' => 'rgba(231,229,228,.75)',
        'stripe1'     => '#a8a29e', 'stripe2'     => '#d6d3d1',
        'idBg'        => 'rgba(214,211,209,.12)', 'idBorder' => 'rgba(214,211,209,.35)',
    ],
    default            => [
        'bg'          => 'linear-gradient(145deg,#0a0101,#160202,#0a0101)',
        'o1'          => '#b91c1c', 'o2'          => '#ef4444',
        'accent'      => '#fca5a5', 'accentMuted' => 'rgba(252,165,165,.18)',
        'accentBorder'=> 'rgba(252,165,165,.35)',
        'clockColor'  => '#fecaca', 'courseColor' => 'rgba(254,202,202,.8)',
        'stripe1'     => '#f87171', 'stripe2'     => '#fca5a5',
        'idBg'        => 'rgba(252,165,165,.15)', 'idBorder' => 'rgba(252,165,165,.4)',
    ],
};

$_themeBadge = match($_theme){
    'christmas'        => ['🎄','Merry Christmas!'],
    'summer'           => ['☀️','Happy Summer!'],
    'rainy_season'     => ['🌧️','Stay safe this rainy season'],
    'holy_week'        => ['✝️','Blessed Holy Week'],
    'undas'            => ['🕯️','All Saints Day'],
    'new_year'         => ['🎆','Happy New Year!'],
    'independence_day' => ['🇵🇭','Happy Independence Day!'],
    'halloween'        => ['🎃','Happy Halloween!'],
    default            => null,
};
$_daysUntil = null;
if ($nextAppointment && $nextAppointment->next_consultation) {
    $_daysUntil = (int) now()->startOfDay()->diffInDays($nextAppointment->next_consultation->startOfDay(), false);
}
@endphp

{{-- ── HERO ── --}}
<div class="hero-shell afu d0"
     style="
       background:{{ $_palette['bg'] }};
       --gpill-color:{{ $_palette['accent'] }};
       --gpill-bg:{{ $_palette['accentMuted'] }};
       --gpill-border:{{ $_palette['accentBorder'] }};
       --clock-color:{{ $_palette['clockColor'] }};
       --course-color:{{ $_palette['courseColor'] }};
       --id-color:{{ $_palette['accent'] }};
       --id-bg:{{ $_palette['idBg'] }};
       --id-border:{{ $_palette['idBorder'] }};
       --stripe1:{{ $_palette['stripe1'] }};
       --stripe2:{{ $_palette['stripe2'] }};
     ">

    {{-- Gradient wash --}}
    <div class="absolute inset-0 z-0" style="background:radial-gradient(ellipse 70% 75% at 18% 22%,{{ $_palette['o1'] }}44,transparent 55%),radial-gradient(ellipse 52% 60% at 82% 82%,{{ $_palette['o2'] }}33,transparent 55%);background-size:400% 400%;animation:gradShift 13s ease infinite"></div>

    <canvas id="heroCanvas"></canvas>

    {{-- Orbs --}}
    <div class="hero-orb" style="width:260px;height:260px;background:{{ $_palette['o1'] }};top:-80px;right:55px;filter:blur(68px);opacity:.22;animation-duration:9s"></div>
    <div class="hero-orb" style="width:200px;height:200px;background:{{ $_palette['o2'] }};bottom:-50px;right:270px;filter:blur(64px);opacity:.18;animation-duration:12s;animation-delay:-5s"></div>
    <div class="hero-orb" style="width:140px;height:140px;background:#fff;top:25px;right:440px;filter:blur(74px);opacity:.06;animation-duration:16s;animation-delay:-9s"></div>

    <div class="hero-grid"></div>
    <div class="hero-vig"></div>

    {{-- Theme badge (top right) --}}
    @if($_themeBadge)
    <div class="absolute top-4 right-5 z-10">
        <span class="inline-flex items-center gap-1.5 backdrop-blur-xl border text-[.72rem] font-semibold px-3 py-1.5 rounded-full"
              style="background:{{ $_palette['accentMuted'] }};border-color:{{ $_palette['accentBorder'] }};color:{{ $_palette['accent'] }};animation:glowB 3s ease infinite">
            {{ $_themeBadge[0] }} {{ $_themeBadge[1] }}
        </span>
    </div>
    @endif

    {{-- HERO BODY --}}
    <div class="hero-body">
        <div class="flex items-end justify-between gap-6">

            {{-- LEFT CONTENT --}}
            <div class="flex-1 min-w-0">

                {{-- ROW 1: Greeting pill + Clock chip --}}
                <div class="flex items-center flex-wrap gap-2.5 mb-3.5">
                    <span id="timeGreeting" class="hero-greeting-pill">Good morning,</span>
                    <div class="hero-clock-chip">
                        <span id="heroClock" class="ck-time">00:00:00</span>
                        <span class="ck-sep">·</span>
                        <span id="heroDate" class="ck-date"></span>
                    </div>
                </div>

                {{-- ROW 2: BIG NAME --}}
                <div class="overflow-visible mb-2">
                    <h1 id="heroName" class="font-black text-white leading-none tracking-tight select-none"
                        style="font-size:clamp(2.6rem,7vw,4.5rem);text-shadow:0 2px 40px rgba(0,0,0,.5)">
                        {{ $user->first_name }}
                    </h1>
                </div>

                {{-- ROW 3: ID + Course --}}
                <div class="flex items-center gap-2 flex-wrap mb-3.5">
                    <span class="hero-id-tag">{{ $user->id_number }}</span>
                    <span class="hero-meta-sep">·</span>
                    <span class="hero-course">{{ $user->course_label }}</span>
                </div>

                {{-- ROW 4: Mini stats --}}
                <div class="flex items-center gap-2 flex-wrap mb-4">
                    <div class="h-stat-chip">
                        <span class="h-stat-num">{{ $totalVisits }}</span>
                        <span class="h-stat-lbl">total visits</span>
                    </div>
                    <div class="h-stat-chip">
                        <span class="h-stat-num">{{ $apptSummary['Completed'] ?? 0 }}</span>
                        <span class="h-stat-lbl">completed</span>
                    </div>
                    @if(($_apptSummary = $apptSummary['Upcoming'] ?? 0) > 0)
                    <div class="h-stat-chip">
                        <span class="h-stat-num">{{ $_apptSummary }}</span>
                        <span class="h-stat-lbl">upcoming</span>
                    </div>
                    @endif
                </div>

                {{-- ROW 5: Action pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Clinic status --}}
                    <span class="s-pill {{ $settings->clinic_status==='open'
                        ? 'bg-emerald-500/14 text-emerald-200 border-emerald-500/22'
                        : 'bg-red-400/14 text-red-200 border-red-400/22' }}">
                        <span class="s-dot-wrap">
                            <span class="s-dot {{ $settings->clinic_status==='open' ? 'bg-emerald-400 text-emerald-400' : 'bg-red-400 text-red-400' }}"></span>
                            <span class="s-ring {{ $settings->clinic_status==='open' ? 'border-emerald-400' : 'border-red-400' }}"></span>
                            <span class="s-ring s-ring-2 {{ $settings->clinic_status==='open' ? 'border-emerald-400' : 'border-red-400' }}"></span>
                        </span>
                        Clinic {{ strtoupper($settings->clinic_status) }}
                        @if($settings->clinic_hours) &nbsp;·&nbsp; {{ $settings->clinic_hours }} @endif
                    </span>

                    {{-- Countdown badge --}}
                    @if($_daysUntil !== null && $_daysUntil >= 0)
                    <span class="cd-badge {{ $_daysUntil===0 ? 'cd-today' : ($nextAppointment->status==='Pending' ? 'cd-pend' : 'cd-def') }}">
                        📅 @if($_daysUntil===0) Today! @elseif($_daysUntil===1) Tomorrow @else In {{ $_daysUntil }} days @endif
                    </span>
                    @endif

                    {{-- Book button --}}
                    @if($settings->clinic_status === 'open')
                    <button onclick="document.getElementById('apptModal').classList.remove('hidden')" class="btn-hero magnetic">
                        + Book Appointment
                    </button>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Floating logo --}}
            <div class="shrink-0 hidden sm:flex flex-col items-center gap-3">
                <div style="animation:floatY 5.5s ease-in-out infinite">
                    <img src="{{ asset('images/um_logo_no_bg.png') }}" alt="UM Clinic"
                         class="w-20 h-20 lg:w-24 lg:h-24 object-contain select-none"
                         style="filter:drop-shadow(0 0 28px {{ $_palette['accent'] }}44) drop-shadow(0 14px 28px rgba(0,0,0,.35))">
                </div>
                {{-- Glow ring under logo --}}
                <div class="w-16 h-2 rounded-full opacity-40" style="background:radial-gradient(ellipse at center,{{ $_palette['accent'] }},transparent 70%);filter:blur(4px)"></div>
            </div>
        </div>
    </div>

    {{-- Theme-colored bottom accent stripe --}}
    <div class="hero-stripe"></div>
</div>

{{-- ══════════════════════════════════════════
     WELLNESS MARQUEE — TOP
══════════════════════════════════════════ --}}
@php
$_tips = [
    ['💧','Stay Hydrated','Drink 8+ glasses of water daily'],
    ['🥗','Balanced Diet','Fruits, veggies & lean proteins'],
    ['😴','Quality Sleep','7–9 hours every night'],
    ['🏃','Stay Active','30 min of exercise most days'],
    ['🧘','Manage Stress','Deep breaths, short walks help'],
    ['🤲','Good Hygiene','Wash hands to stay illness-free'],
    ['☀️','Get Sunlight','Boosts vitamin D & lifts mood'],
    ['🚭','Avoid Smoking','Keep your lungs strong & clean'],
];
@endphp
<div class="marq-shell afu d1">
    <div class="marq-track">
        @for($i=0;$i<2;$i++)
            @foreach($_tips as [$ico,$title,$desc])
            <div class="m-item">
                <span class="m-sep"></span>
                <span>{{ $ico }}</span>
                <strong>{{ $title }}</strong>
                <span style="opacity:.65">— {{ $desc }}</span>
            </div>
            @endforeach
        @endfor
    </div>
</div>

{{-- ══════════════════════════════════════════
     STAT CARDS — tall portrait, reference style
══════════════════════════════════════════ --}}
@php
$_pending   = $apptSummary['Pending']   ?? 0;
$_upcoming  = $apptSummary['Upcoming']  ?? 0;
$_completed = $apptSummary['Completed'] ?? 0;
$_cancelled = $apptSummary['Cancelled'] ?? 0;
$_totalAppts = max($_pending + $_upcoming + $_completed + $_cancelled, 1);
$_completionPct = (int) min(100, ($_completed / max($totalVisits, 1)) * 100);
$_yearPct = (int) min(100, ($visitsThisYear / max($totalVisits, 1)) * 100);
@endphp

<div class="stat-grid">

    {{-- ── Card 1: Total Visits (Blue) ── --}}
    <div class="scard afu d2"
         style="background:linear-gradient(155deg,#1e3a8a,#2563eb,#3b82f6);--sh:rgba(37,99,235,.55)">

        <div class="sc-label">Total Visits</div>
        <div class="sc-num" data-target="{{ $totalVisits }}">{{ $totalVisits }}</div>
        <p class="sc-desc">
            Your cumulative clinic consultations across all appointments since enrollment.
        </p>

        {{-- Bar chart: appointment status breakdown --}}
        <div class="sc-chart">
            <div class="sc-bars">
                @foreach([
                    ['Pend', $_pending,   false],
                    ['Upco', $_upcoming,  false],
                    ['Done', $_completed, true ],
                    ['Canc', $_cancelled, false],
                ] as [$lbl, $v, $hi])
                @php $barH = $_totalAppts > 0 ? max(6, ($v / $_totalAppts) * 72) : 6; @endphp
                <div class="sc-bar-wrap">
                    <div class="sc-bar {{ $hi ? 'hi' : '' }}"
                         data-h="{{ $barH }}px">
                        <span class="sc-bar-v">{{ $v }}</span>
                    </div>
                    <span class="sc-bar-l">{{ $lbl }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="sc-foot">
            <hr class="sc-divider">
            <a href="{{ route('student.history') }}" class="sc-cta">
                View Full History
                <span class="sc-arrow">→</span>
            </a>
        </div>
    </div>

    {{-- ── Card 2: Visits This Year (Teal/Green) ── --}}
    <div class="scard afu d3"
         style="background:linear-gradient(155deg,#064e3b,#059669,#34d399);--sh:rgba(5,150,105,.55)">

        <div class="sc-label">Visits This Year</div>
        <div class="sc-num" data-target="{{ $visitsThisYear }}">{{ $visitsThisYear }}</div>
        <p class="sc-desc">
            Clinic consultations recorded for {{ now()->year }}, out of your all-time total.
        </p>

        {{-- Progress bar: this year vs total --}}
        <div class="sc-prog">
            <div class="sc-prog-info">
                <span class="sc-prog-lbl">{{ now()->year }} out of all-time</span>
                <span class="sc-prog-pct">{{ $_yearPct }}%</span>
            </div>
            <div class="sc-prog-track">
                <div class="sc-prog-fill" data-w="{{ $_yearPct }}%"></div>
            </div>
        </div>

        {{-- Mini bars: year labels (last 3 years) --}}
        <div class="sc-chart">
            <div class="sc-bars">
                @php
                $_yrs = [now()->year - 2, now()->year - 1, now()->year];
                @endphp
                @foreach($_yrs as $_yr)
                @php
                $isThis = $_yr === (int)now()->year;
                $v = $isThis ? $visitsThisYear : 0;
                $h = $isThis ? max(40, ($v / max($totalVisits,1)) * 72) : 8;
                @endphp
                <div class="sc-bar-wrap">
                    <div class="sc-bar {{ $isThis ? 'hi' : '' }}"
                         data-h="{{ $h }}px">
                        @if($isThis)<span class="sc-bar-v">{{ $v }}</span>@endif
                    </div>
                    <span class="sc-bar-l">{{ $_yr }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="sc-foot">
            <hr class="sc-divider">
            <a href="{{ route('student.history') }}" class="sc-cta">
                View Records
                <span class="sc-arrow">→</span>
            </a>
        </div>
    </div>

    {{-- ── Card 3: Upcoming (Amber / Gold) ── --}}
    <div class="scard afu d4"
         style="background:linear-gradient(155deg,#78350f,#d97706,#fbbf24);--sh:rgba(217,119,6,.58)">

        <div class="sc-label">Upcoming</div>
        <div class="sc-num" data-target="{{ $_upcoming }}">{{ $_upcoming }}</div>
        <p class="sc-desc">
            Appointments confirmed and scheduled for your upcoming clinic visits.
        </p>

        {{-- Bar chart: pending vs upcoming --}}
        <div class="sc-chart">
            <div class="sc-bars">
                @foreach([
                    ['Pending',  $_pending,  false],
                    ['Upcoming', $_upcoming, true ],
                ] as [$lbl, $v, $hi])
                @php $bMax = max($_pending, $_upcoming, 1); $bH = max(8, ($v / $bMax) * 72); @endphp
                <div class="sc-bar-wrap">
                    <div class="sc-bar {{ $hi ? 'hi' : '' }}"
                         data-h="{{ $bH }}px">
                        <span class="sc-bar-v">{{ $v }}</span>
                    </div>
                    <span class="sc-bar-l">{{ $lbl }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="sc-foot">
            <hr class="sc-divider">
            <a href="{{ route('student.appointments') }}" class="sc-cta">
                View Appointments
                <span class="sc-arrow">→</span>
            </a>
        </div>
    </div>

    {{-- ── Card 4: Completed (Purple) ── --}}
    <div class="scard afu d5"
         style="background:linear-gradient(155deg,#3b0764,#7c3aed,#a78bfa);--sh:rgba(124,58,237,.55)">

        <div class="sc-label">Completed</div>
        <div class="sc-num" data-target="{{ $_completed }}">{{ $_completed }}</div>
        <p class="sc-desc">
            Successfully concluded clinic appointments with medical records created.
        </p>

        {{-- Completion rate progress --}}
        <div class="sc-prog">
            <div class="sc-prog-info">
                <span class="sc-prog-lbl">Completion rate</span>
                <span class="sc-prog-pct">{{ $_completionPct }}%</span>
            </div>
            <div class="sc-prog-track">
                <div class="sc-prog-fill violet" data-w="{{ $_completionPct }}%"></div>
            </div>
        </div>

        {{-- Done vs Cancelled bars --}}
        <div class="sc-chart">
            <div class="sc-bars">
                @foreach([
                    ['Done',  $_completed, true ],
                    ['Cancl', $_cancelled, false],
                ] as [$lbl, $v, $hi])
                @php $bMax = max($_completed, $_cancelled, 1); $bH = max(8, ($v / $bMax) * 72); @endphp
                <div class="sc-bar-wrap">
                    <div class="sc-bar {{ $hi ? 'hi' : '' }}"
                         data-h="{{ $bH }}px">
                        <span class="sc-bar-v">{{ $v }}</span>
                    </div>
                    <span class="sc-bar-l">{{ $lbl }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="sc-foot">
            <hr class="sc-divider">
            <a href="{{ route('student.history') }}" class="sc-cta">
                See All Records
                <span class="sc-arrow">→</span>
            </a>
        </div>
    </div>

</div>{{-- /stat-grid --}}

{{-- ══════════════════════════════════════════
     QUICK ACCESS
══════════════════════════════════════════ --}}
<div class="quick-wrap afu d6">
    <div class="quick-head">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-gradient-to-br from-red-100 to-rose-100 border border-red-100 rounded-lg flex items-center justify-center text-sm">⚡</div>
            <div>
                <div class="font-bold text-slate-800 text-sm leading-tight">Quick Access</div>
                <div class="c-eyebrow text-slate-400">Navigate fast</div>
            </div>
        </div>
    </div>
    <div class="px-4 py-3.5">
        <div class="quick-pills">
            @foreach([
                [route('student.appointments'), '📅','Appointments'],
                [route('student.history'),      '🗂','My History'],
                [route('student.health_safety'),'🛡','Health & Safety'],
                [route('student.announcements'),'📢','Announcements'],
                [route('student.messages'),     '✉️','Clinic Inbox'],
                [route('student.feedback'),     '📝','Feedback'],
            ] as [$href,$ico,$lbl])
            <a href="{{ $href }}" class="q-pill">
                <span class="q-pill-ico">{{ $ico }}</span>
                <span>{{ $lbl }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     BENTO GRID
══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">

    {{-- ── NEXT APPOINTMENT (dark card) ── --}}
    <div class="b-card afu d7 flex flex-col {{ $nextAppointment && $nextAppointment->next_consultation ? 'appt-card-has' : 'appt-card' }}" style="border:1px solid rgba(255,255,255,.06)">
        <div class="c-head">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm" style="background:rgba(255,255,255,.1)">📅</div>
                <div>
                    <div class="font-bold text-white text-sm leading-tight">Next Appointment</div>
                    <div class="c-eyebrow text-white/30">{{ $nextAppointment ? $nextAppointment->status : 'No upcoming' }}</div>
                </div>
            </div>
            @if($nextAppointment)
            <span class="text-[.7rem] px-2.5 py-1 rounded-full font-bold border
                {{ strtolower($nextAppointment->status)==='upcoming'
                    ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/25'
                    : 'bg-amber-500/20 text-amber-300 border-amber-500/25' }}">
                {{ $nextAppointment->status }}
            </span>
            @endif
        </div>

        <div class="p-5 flex-1">
            @if($nextAppointment && $nextAppointment->next_consultation)
            <div class="space-y-2.5">
                @foreach([
                    ['🗓','rgba(59,130,246,.2)',  'border-blue-500/20',    'Date',   $nextAppointment->next_consultation->format('F j, Y')],
                    ['👨‍⚕️','rgba(167,139,250,.2)','border-violet-500/20', 'Doctor', $nextAppointment->doctor ?: 'TBA'],
                    ['🩺','rgba(52,211,153,.2)',   'border-emerald-500/20', 'Reason', $nextAppointment->reason ?: 'N/A'],
                ] as [$ic,$ibg,$ibd,$lbl,$val])
                <div class="appt-meta-row">
                    <div class="appt-meta-ico border {{ $ibd }}" style="background:{{ $ibg }}">{{ $ic }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="text-[.63rem] text-white/35 font-bold uppercase tracking-wider">{{ $lbl }}</div>
                        <div class="font-bold text-white text-sm truncate mt-0.5">{{ $val }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('student.appointments') }}" class="btn-outline-dark">View All Appointments &rarr;</a>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-9 text-center gap-3">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl" style="background:rgba(255,255,255,.08);animation:floatY 4.5s ease-in-out infinite">🗓</div>
                <div>
                    <p class="text-white font-bold text-sm">No upcoming appointment</p>
                    <p class="text-white/35 text-xs mt-0.5">Book one to get started</p>
                </div>
                @if($settings->clinic_status === 'open')
                <button onclick="document.getElementById('apptModal').classList.remove('hidden')" class="btn-book magnetic">+ Request Appointment</button>
                @else
                <span class="text-xs text-white/35" style="background:rgba(255,255,255,.07);padding:.4rem .9rem;border-radius:999px;border:1px solid rgba(255,255,255,.12)">Clinic is closed</span>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ── RECENT CONSULTATIONS ── --}}
    <div class="b-card rec-card afu d8 flex flex-col">
        <div class="c-head-light">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-center text-sm">🗂</div>
                <div>
                    <div class="font-bold text-slate-800 text-sm leading-tight">Recent Consultations</div>
                    <div class="c-eyebrow text-slate-400">Visit history</div>
                </div>
            </div>
            <a href="{{ route('student.history') }}" class="text-[.72rem] font-bold text-red-700 hover:text-red-900 transition">View all →</a>
        </div>
        <div class="flex-1 divide-y divide-slate-50">
            @forelse($recentRecords as $rec)
            <div class="rec-row">
                <div class="w-9 h-9 bg-gradient-to-br from-red-50 to-rose-100 border border-red-100 rounded-xl flex items-center justify-center text-sm shrink-0">🩺</div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">{{ $rec->reason }}</div>
                    <div class="text-[.7rem] text-slate-400 mt-0.5">{{ $rec->date_consulted->format('M d, Y') }}</div>
                </div>
                @if($rec->medicine)
                <span class="text-[.67rem] bg-blue-50 border border-blue-100 text-blue-600 font-semibold px-2 py-1 rounded-lg shrink-0 max-w-[82px] truncate">{{ Str::limit($rec->medicine,10) }}</span>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-center gap-2.5 px-5">
                <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-center text-2xl" style="animation:floatY 5s ease-in-out infinite 1s">📋</div>
                <p class="text-slate-500 text-sm font-bold">No records yet</p>
                <p class="text-slate-300 text-xs">Your visit history appears here</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── HEALTH SNAPSHOT ── --}}
    <div class="b-card health-card afu" style="animation-delay:.59s;flex-direction:column;display:flex">
        <div class="c-head-light">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 bg-pink-50 border border-pink-100 rounded-lg flex items-center justify-center text-sm">💊</div>
                <div>
                    <div class="font-bold text-slate-800 text-sm leading-tight">Health Snapshot</div>
                    <div class="c-eyebrow text-slate-400">Your pattern</div>
                </div>
            </div>
            <span class="text-[.65rem] font-bold px-2.5 py-1 rounded-full bg-gradient-to-r from-rose-50 to-pink-50 border border-rose-100 text-rose-600">Stats</span>
        </div>
        <div class="p-4 flex-1 space-y-2.5">
            <div class="snap-item bg-gradient-to-br from-red-50 to-rose-50 border-red-100">
                <div class="flex items-center gap-1.5 mb-2"><span>🩺</span><span class="text-[.6rem] text-red-500 font-black uppercase tracking-widest">Top Reason</span></div>
                <div class="font-bold text-slate-800 text-sm truncate mb-2">{{ $topReason?->reason ?? 'None yet' }}</div>
                <div class="flex items-center gap-2">
                    <div class="h-bar-track flex-1"><div class="h-bar-fill bg-gradient-to-r from-red-400 to-rose-500" data-width="{{ $topReason ? min(100,($topReason->cnt/max($totalVisits,1))*100) : 0 }}%"></div></div>
                    <span class="text-[.67rem] text-slate-500 font-bold w-6 text-right shrink-0">{{ $topReason?->cnt ?? 0 }}x</span>
                </div>
            </div>
            <div class="snap-item bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-100">
                <div class="flex items-center gap-1.5 mb-2"><span>💊</span><span class="text-[.6rem] text-blue-500 font-black uppercase tracking-widest">Top Medicine</span></div>
                <div class="font-bold text-slate-800 text-sm truncate mb-2">{{ $topMedicine?->medicine ?? 'None yet' }}</div>
                <div class="flex items-center gap-2">
                    <div class="h-bar-track flex-1"><div class="h-bar-fill bg-gradient-to-r from-blue-400 to-indigo-500" data-width="{{ $topMedicine ? min(100,($topMedicine->cnt/max($totalVisits,1))*100) : 0 }}%"></div></div>
                    <span class="text-[.67rem] text-slate-500 font-bold w-6 text-right shrink-0">{{ $topMedicine?->cnt ?? 0 }}x</span>
                </div>
            </div>
            <div class="snap-item bg-gradient-to-br from-emerald-50 to-teal-50 border-emerald-100">
                <div class="flex items-center gap-1.5 mb-1.5"><span>📅</span><span class="text-[.6rem] text-emerald-600 font-black uppercase tracking-widest">Last Visit</span></div>
                <div class="font-bold text-slate-800 text-sm">{{ $lastRecord?->date_consulted?->format('M d, Y') ?? 'No visits yet' }}</div>
                @if($lastRecord)<div class="text-[.67rem] text-slate-400 mt-0.5 truncate">{{ $lastRecord->reason }}</div>@endif
            </div>
        </div>
        <div class="px-4 pb-4">
            <a href="{{ route('student.history') }}" class="btn-outline-light">View Full History</a>
        </div>
    </div>

</div>

{{-- ── APPOINTMENT MODAL ── --}}
<div id="apptModal" class="portal-modal-overlay hidden" onclick="if(event.target===this)closeApptModal()">
    <div class="portal-modal-box wide">
        <div class="portal-modal-header">
            <h3>📅 Request an Appointment</h3>
            <button onclick="closeApptModal()" class="text-slate-400 hover:text-slate-700 transition p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="apptForm" class="portal-modal-body" onsubmit="return false;">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4">
                <div class="f-group">
                    <label class="f-label">Preferred Date <span class="text-slate-400 text-xs font-normal">(optional)</span></label>
                    <input type="date" name="next_consultation" class="f-input" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    <p class="text-xs text-slate-400 mt-1">Staff will confirm or adjust the date.</p>
                </div>
                <div class="f-group">
                    <label class="f-label">Reason for Visit <span class="text-red-500">*</span></label>
                    <select name="reason" class="f-select" required>
                        <option value="">— Select reason —</option>
                        @foreach($reasonOptions as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div id="apptModalError" class="alert-danger hidden text-sm"></div>
        </form>
        <div class="portal-modal-footer">
            <button onclick="closeApptModal()" type="button" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2 rounded-xl transition">Cancel</button>
            <button onclick="submitAppt()" id="apptSubmitBtn" type="button" class="bg-red-700 hover:bg-red-800 text-white text-sm font-semibold px-5 py-2 rounded-xl transition shadow-sm">Submit Request</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/* CURSOR GLOW */
(function(){
  const g=document.getElementById('cursorGlow');
  if(!g)return;
  let tx=window.innerWidth/2,ty=window.innerHeight/2,cx=tx,cy=ty;
  document.addEventListener('mousemove',e=>{tx=e.clientX;ty=e.clientY});
  (function raf(){cx+=(tx-cx)*.11;cy+=(ty-cy)*.11;g.style.left=cx+'px';g.style.top=cy+'px';requestAnimationFrame(raf)})();
})();

/* CANVAS PARTICLES */
(function(){
  const c=document.getElementById('heroCanvas');if(!c)return;
  const shell=c.parentElement,ctx=c.getContext('2d');let W,H,pts=[];
  function resize(){W=c.width=shell.offsetWidth;H=c.height=shell.offsetHeight}
  function P(){this.reset=function(){this.x=Math.random()*W;this.y=Math.random()*H;this.r=Math.random()*1.7+.3;this.vx=(Math.random()-.5)*.4;this.vy=(Math.random()-.5)*.28;this.a=Math.random()*.3+.07;this.life=0;this.max=Math.random()*320+160};this.reset()}
  for(let i=0;i<65;i++){const p=new P();p.life=Math.random()*p.max;pts.push(p)}
  function draw(){ctx.clearRect(0,0,W,H);for(const p of pts){p.life++;if(p.life>p.max)p.reset();const t=p.life/p.max,f=t<.14?t/.14:t>.82?(1-t)/.18:1;ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);ctx.fillStyle=`rgba(255,255,255,${p.a*f})`;ctx.fill();p.x+=p.vx;p.y+=p.vy}requestAnimationFrame(draw)}
  resize();window.addEventListener('resize',resize);draw();
})();

/* LIVE CLOCK */
(function(){
  const clk=document.getElementById('heroClock'),dat=document.getElementById('heroDate');
  const D=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],M=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  function tick(){const n=new Date(),h=String(n.getHours()).padStart(2,'0'),m=String(n.getMinutes()).padStart(2,'0'),s=String(n.getSeconds()).padStart(2,'0');if(clk)clk.textContent=`${h}:${m}:${s}`;if(dat)dat.textContent=`${D[n.getDay()]}, ${M[n.getMonth()]} ${n.getDate()}`}
  tick();setInterval(tick,1000);
})();

/* GREETING */
(function(){
  const h=new Date().getHours(),el=document.getElementById('timeGreeting');
  if(el)el.textContent=h<12?'Good morning,':h<18?'Good afternoon,':'Good evening,';
})();

/* NAME REVEAL */
(function(){
  const el=document.getElementById('heroName');if(!el)return;
  const t=el.textContent.trim();el.innerHTML='';
  [...t].forEach((ch,i)=>{
    const w=document.createElement('span');w.className='c-clip';
    const s=document.createElement('span');s.className='c-char';
    s.style.animationDelay=`${.1+i*.055}s`;s.textContent=ch===' '?' ':ch;
    w.appendChild(s);el.appendChild(w);
  });
  const wav=document.createElement('span');
  wav.style.cssText='display:inline-block;margin-left:.2em;animation:spinFloat 3.2s ease-in-out infinite;animation-delay:.75s';
  wav.textContent='👋';el.appendChild(wav);
})();

/* MAGNETIC */
(function(){
  document.querySelectorAll('.magnetic').forEach(el=>{
    el.addEventListener('mousemove',e=>{const r=el.getBoundingClientRect();el.style.transform=`translate(${(e.clientX-r.left-r.width/2)*.3}px,${(e.clientY-r.top-r.height/2)*.3}px)`});
    el.addEventListener('mouseleave',()=>el.style.transform='');
  });
})();

/* STAT COUNTERS */
(function(){
  const ease=t=>1-Math.pow(1-t,3);
  const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{
      if(!e.isIntersecting)return;
      const el=e.target,target=parseInt(el.dataset.target||'0',10);
      if(!target){obs.unobserve(el);return}
      const dur=950+target*14,t0=performance.now();
      (function step(now){const p=Math.min((now-t0)/dur,1);el.textContent=Math.round(ease(p)*target);if(p<1)requestAnimationFrame(step);else obs.unobserve(el)})(performance.now());
    });
  },{threshold:.35});
  document.querySelectorAll('.snum').forEach(el=>obs.observe(el));
})();

/* HEALTH BARS (health snapshot) */
(function(){
  const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(!e.isIntersecting)return;setTimeout(()=>e.target.style.width=e.target.dataset.width||'0%',220);obs.unobserve(e.target)});
  },{threshold:.3});
  document.querySelectorAll('.h-bar-fill').forEach(el=>obs.observe(el));
})();

/* STAT CARD BARS — animate height on scroll into view */
(function(){
  const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{
      if(!e.isIntersecting)return;
      const card=e.target;
      /* animate vertical bars */
      card.querySelectorAll('.sc-bar').forEach((bar,i)=>{
        setTimeout(()=>{ bar.style.height=bar.dataset.h||'8px'; },i*80);
      });
      /* animate horizontal progress fills */
      card.querySelectorAll('.sc-prog-fill').forEach(fill=>{
        setTimeout(()=>{ fill.style.width=fill.dataset.w||'0%'; },200);
      });
      obs.unobserve(card);
    });
  },{threshold:.25});
  document.querySelectorAll('.scard').forEach(el=>obs.observe(el));
})();

/* SCROLL REVEAL */
(function(){
  const obs=new IntersectionObserver(entries=>{entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');obs.unobserve(e.target)}})},{threshold:.08});
  document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
})();

/* 3D TILT */
(function(){
  if(window.matchMedia('(pointer:coarse)').matches)return;
  document.querySelectorAll('.scard,.b-card').forEach(c=>{
    c.addEventListener('mousemove',e=>{
      const r=c.getBoundingClientRect();
      const dx=(e.clientX-r.left-r.width/2)/(r.width/2),dy=(e.clientY-r.top-r.height/2)/(r.height/2);
      const lift=c.classList.contains('scard')?-8:-5;
      c.style.transform=`perspective(960px) rotateX(${-dy*5}deg) rotateY(${dx*5}deg) translateY(${lift}px) scale(1.015)`;
    });
    c.addEventListener('mouseleave',()=>c.style.transform='');
  });
})();

/* MODAL */
function closeApptModal(){document.getElementById('apptModal').classList.add('hidden');document.getElementById('apptForm').reset();document.getElementById('apptModalError').classList.add('hidden')}
function submitAppt(){
  const form=document.getElementById('apptForm'),btn=document.getElementById('apptSubmitBtn'),err=document.getElementById('apptModalError'),data=new FormData(form);
  if(!data.get('reason')){err.textContent='Please select a reason.';err.classList.remove('hidden');return}
  btn.disabled=true;btn.textContent='Submitting…';
  fetch('{{ route("student.appointments.request") }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},body:data})
  .then(r=>r.json()).then(d=>{if(d.success){closeApptModal();location.reload()}else{err.textContent=d.message||'Failed.';err.classList.remove('hidden');btn.disabled=false;btn.textContent='Submit Request'}})
  .catch(()=>{err.textContent='Network error.';err.classList.remove('hidden');btn.disabled=false;btn.textContent='Submit Request'});
}
function respondAppointment(id,action,btn){
  if(!confirm(`Are you sure you want to ${action} this appointment?`))return;
  btn.disabled=true;btn.style.opacity='.65';
  fetch('{{ route("student.appointments.action") }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({appointment_id:id,action:action})})
  .then(r=>r.json()).then(d=>{if(d.success)location.reload();else{alert(d.message||'Error');btn.disabled=false;btn.style.opacity='1'}})
  .catch(()=>{alert('Network error');btn.disabled=false;btn.style.opacity='1'});
}
</script>
@endsection
