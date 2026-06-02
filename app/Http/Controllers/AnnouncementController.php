<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\User;

class AnnouncementController extends Controller
{
    private const SEASON_DATA = [
        'summer' => [
            'label' => 'Summer / Dry Season', 'period' => 'Mar – May', 'icon' => '☀️',
            'templates' => [
                ['label' => 'Heat Advisory',    'title' => 'Summer Heat Advisory',
                 'body'  => "With temperatures rising during the summer season, all students and staff are advised to:\n\n• Drink at least 8 glasses of water daily\n• Avoid prolonged sun exposure from 10 AM to 4 PM\n• Wear light, breathable clothing\n• Visit the clinic if you experience dizziness, headache, or heat cramps\n\nStay cool and stay healthy!"],
                ['label' => 'Chickenpox Alert', 'title' => 'Chickenpox Season Reminder',
                 'body'  => "Summer is peak season for chickenpox. If you develop a rash with blisters:\n\n• Stay home and avoid contact with others immediately\n• Report to the clinic for assessment\n• Do not scratch blisters to prevent secondary infection\n\nEarly consultation helps prevent campus-wide spread."],
            ],
        ],
        'rainy' => [
            'label' => 'Rainy / Typhoon Season', 'period' => 'Jun – Oct', 'icon' => '🌧️',
            'templates' => [
                ['label' => 'Dengue Alert',  'title' => 'Dengue Fever Alert — Rainy Season',
                 'body'  => "The rainy season increases the risk of dengue fever. Please take the following precautions:\n\n• Eliminate stagnant water in and around your homes\n• Use mosquito repellent, especially at dawn and dusk\n• Wear long-sleeved clothing when possible\n• Seek immediate medical attention for high fever (39°C+) lasting 2 or more days\n\nReport suspected dengue symptoms to the clinic immediately."],
                ['label' => 'Flood Safety', 'title' => 'Flood Safety & Leptospirosis Prevention',
                 'body'  => "During and after flooding, protect yourself from waterborne diseases:\n\n• Avoid direct contact with floodwater\n• Wear rubber boots and gloves if contact is unavoidable\n• Disinfect any wound exposed to floodwater\n• Boil drinking water or use bottled water\n• Consult the clinic if you develop fever, headache, or body pain after flood exposure"],
            ],
        ],
        'ber_months' => [
            'label' => '"Ber" Months / Holidays', 'period' => 'Nov – Dec', 'icon' => '🎄',
            'templates' => [
                ['label' => 'Flu Season Alert',    'title' => 'Flu Season Advisory — "Ber" Months',
                 'body'  => "As the holiday season begins, so does flu season. Protect yourself and others:\n\n• Get your annual flu vaccine if you haven't yet\n• Wash hands frequently with soap and water\n• Avoid close contact with sick individuals\n• Stay home when feeling unwell\n• Visit the clinic for any persistent respiratory symptoms\n\nStay healthy this holiday season!"],
                ['label' => 'Holiday Wellness', 'title' => 'Holiday Wellness Reminder',
                 'body'  => "The holidays are a time for joy — and for taking care of your health! Remember to:\n\n• Eat balanced meals and drink plenty of water\n• Get 7–9 hours of sleep nightly\n• Manage stress through rest, exercise, and social connection\n• Avoid excessive food and alcohol intake\n\nThe clinic is open for any health concerns. Happy holidays!"],
            ],
        ],
        'new_year' => [
            'label' => 'New Year / Cool Season', 'period' => 'Jan – Feb', 'icon' => '🎆',
            'templates' => [
                ['label' => 'Firecracker Safety',    'title' => 'New Year Firecracker Safety Warning',
                 'body'  => "As we welcome the New Year, please exercise caution with fireworks:\n\n• Maintain a safe distance of at least 15 meters from fireworks\n• Only use legal, low-hazard fireworks\n• Never allow children to handle fireworks unsupervised\n• Have a first-aid kit and water source nearby\n• Seek emergency care immediately for any burn or blast injury\n\nThe clinic will be on alert for firecracker-related cases."],
                ['label' => 'New Year Health Reset', 'title' => 'Start the Year Healthy!',
                 'body'  => "Happy New Year! As we begin a fresh year, let's commit to better health:\n\n• Schedule a health check-up early in the year\n• Review and update your vaccinations\n• Set realistic, achievable health goals\n• Stay physically active and eat balanced meals\n\nThe clinic is ready to support your health journey this year!"],
            ],
        ],
    ];

    // ── Staff / Admin: list announcements ───────────────────
    public function staffIndex()
    {
        $announcements = Announcement::with('poster')
            ->orderByDesc('created_at')
            ->paginate(20);

        $this->stampLastSeen();

        $season      = $this->detectSeason((int) now()->format('n'));
        $templates   = self::SEASON_DATA[$season]['templates'];
        $seasonLabel = self::SEASON_DATA[$season]['label'];
        $seasonIcon  = self::SEASON_DATA[$season]['icon'];

        return view('staff.announcements', compact('announcements', 'templates', 'seasonLabel', 'seasonIcon'));
    }

    // ── Staff / Admin: post announcement ───────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        Announcement::create([
            'title'     => $request->title,
            'body'      => $request->body,
            'posted_by' => Auth::user()->id_number,
            'created_at'=> now(),
        ]);

        if (Auth::user()->role === 'superadmin') {
            AuditLog::record(Auth::user()->id_number, 'superadmin', 'Post Announcement', "Posted: {$request->title}");
        }

        return back()->with('success', 'Announcement posted.');
    }

    // ── Staff: delete announcement ──────────────────────────
    public function destroy(int $id)
    {
        Announcement::findOrFail($id)->delete();
        return back()->with('success', 'Announcement deleted.');
    }

    // ── Student: view announcements ─────────────────────────
    public function studentIndex()
    {
        $announcements = Announcement::with('poster')->orderByDesc('created_at')->get();
        $this->stampLastSeen();
        return view('student.announcements', compact('announcements'));
    }

    // ── AJAX: unread count ──────────────────────────────────
    public function unreadCount()
    {
        $user     = Auth::user();
        $lastSeen = $user->announcements_last_seen;

        $count = $lastSeen
            ? Announcement::where('created_at', '>', $lastSeen)->count()
            : Announcement::count();

        return response()->json(['count' => $count]);
    }

    // ── AJAX: mark read (bell click or page visit) ──────────
    public function markRead()
    {
        $this->stampLastSeen();
        return response()->json(['success' => true]);
    }

    // ── Helper: persist last-seen to DB ─────────────────────
    private function stampLastSeen(): void
    {
        Auth::user()->update(['announcements_last_seen' => now()]);
    }

    // ── Helper: detect current season key ───────────────────
    private function detectSeason(int $month): string
    {
        return match (true) {
            in_array($month, [1, 2])           => 'new_year',
            in_array($month, [3, 4, 5])        => 'summer',
            in_array($month, [6, 7, 8, 9, 10]) => 'rainy',
            default                            => 'ber_months',
        };
    }
}
