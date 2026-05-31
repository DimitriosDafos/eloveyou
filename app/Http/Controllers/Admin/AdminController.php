<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\Photo;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUsers'    => User::count(),
            'activeToday'   => User::whereDate('updated_at', today())->count(),
            'pendingReports'=> Report::where('status','pending')->count(),
            'pendingPhotos' => Photo::where('status','pending')->count(),
            'revenue30d'    => Payment::where('status','completed')->where('created_at','>=',now()->subDays(30))->sum('amount'),
        ]);
    }

    public function users(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where('username','like','%'.$request->search.'%'))
            ->when($request->status === 'banned', fn($q) => $q->where('is_banned',true))
            ->when($request->status === 'suspended', fn($q) => $q->where('is_suspended',true))
            ->latest()->paginate(25);
        return view('admin.users', compact('users'));
    }

    public function userDetail(int $id)
    {
        $profile = User::with(['photos','practices','payments','subscriptions'])->findOrFail($id);
        return view('admin.user-detail', compact('profile'));
    }

    public function ban(Request $request, int $id)
    {
        User::findOrFail($id)->update(['is_banned' => true, 'is_suspended' => false]);
        return back()->with('success', __('admin.user_banned'));
    }

    public function suspend(Request $request, int $id)
    {
        User::findOrFail($id)->update(['is_suspended' => true]);
        return back()->with('success', __('admin.user_suspended'));
    }

    public function restore(Request $request, int $id)
    {
        User::findOrFail($id)->update(['is_banned' => false, 'is_suspended' => false]);
        return back()->with('success', __('admin.user_restored'));
    }

    public function reports()
    {
        $reports = Report::with(['reporter','reportedUser','photo','message'])->where('status','pending')->latest()->paginate(20);
        return view('admin.reports', compact('reports'));
    }

    public function resolveReport(Request $request, int $id)
    {
        $request->validate(['action' => 'required|in:resolved,dismissed', 'notes' => 'nullable|string|max:500']);
        Report::findOrFail($id)->update([
            'status' => $request->action, 'reviewed_by' => auth()->id(),
            'admin_notes' => $request->notes, 'reviewed_at' => now(),
        ]);
        return back()->with('success', __('admin.report_resolved'));
    }

    public function photos()
    {
        $photos = Photo::with('user')->where('status','pending')->latest()->paginate(20);
        return view('admin.photos', compact('photos'));
    }

    public function approvePhoto(int $id)
    {
        Photo::findOrFail($id)->update(['status' => 'approved']);
        return back()->with('success', __('admin.photo_approved'));
    }

    public function removePhoto(Request $request, int $id)
    {
        $request->validate(['reason' => 'required|string|max:200']);
        Photo::findOrFail($id)->update(['status' => 'removed', 'removal_reason' => $request->reason]);
        return back()->with('success', __('admin.photo_removed'));
    }

    public function payments()
    {
        $payments = Payment::with('user')->latest()->paginate(30);
        $total = Payment::where('status','completed')->sum('amount');
        return view('admin.payments', compact('payments', 'total'));
    }

    public function stats()
    {
        return view('admin.stats', [
            'registrationsPerDay' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('date','desc')->limit(30)->get(),
            'revenuePerDay'       => Payment::selectRaw('DATE(created_at) as date, SUM(amount) as total')->where('status','completed')->groupBy('date')->orderBy('date','desc')->limit(30)->get(),
        ]);
    }

    public function admins()
    {
        $admins = User::whereNotNull('admin_role')->get();
        return view('admin.admins', compact('admins'));
    }

    public function addAdmin(Request $request)
    {
        $request->validate(['username' => 'required|exists:users,username', 'role' => 'required|in:photo_moderator,finance_staff,profile_moderator,ban_staff,super_admin']);
        User::where('username', $request->username)->update(['admin_role' => $request->role]);
        return back()->with('success', __('admin.admin_added'));
    }

    public function removeAdmin(int $id)
    {
        if ($id === auth()->id()) return back()->withErrors(['error' => __('admin.cannot_remove_self')]);
        User::findOrFail($id)->update(['admin_role' => null]);
        return back()->with('success', __('admin.admin_removed'));
    }
}
