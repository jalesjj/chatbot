<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistics
        $totalUsers = User::count();
        $newRegistrations = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $bannedUsers = User::where('status', 'banned')->count();
        $totalChats = ChatHistory::count();

        // User growth chart data (last 7 days)
        $userGrowth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $userGrowth[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
                'label' => $date->format('d M')
            ];
        }

        // Recent users
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 
            'newRegistrations', 
            'bannedUsers', 
            'totalChats', 
            'userGrowth', 
            'recentUsers'
        ));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->withCount('chatHistories')
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function userDetail($id)
    {
        $user = User::with(['chatHistories' => function($query) {
            $query->orderBy('created_at', 'desc')->take(20);
        }])->findOrFail($id);

        $userStats = [
            'total_chats' => $user->chatHistories()->count(),
            'total_sessions' => $user->chatHistories()->distinct('session_id')->count(),
            'first_chat' => $user->chatHistories()->orderBy('created_at', 'asc')->first()?->created_at,
            'last_chat' => $user->chatHistories()->orderBy('created_at', 'desc')->first()?->created_at,
        ];

        return view('admin.user-detail', compact('user', 'userStats'));
    }

    public function banUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat memblokir user admin'
                ], 403);
            }

            $user->update(['status' => 'banned']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diblokir'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memblokir user'
            ], 500);
        }
    }

    public function unbanUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diaktifkan kembali'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengaktifkan user'
            ], 500);
        }
    }

    public function changeRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:user,admin'
        ]);

        try {
            $user = User::findOrFail($id);
            
            // Tidak bisa mengubah role diri sendiri
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengubah role diri sendiri'
                ], 403);
            }

            $user->update(['role' => $request->role]);

            return response()->json([
                'success' => true,
                'message' => 'Role user berhasil diubah'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah role user'
            ], 500);
        }
    }

    public function deleteUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus user admin'
                ], 403);
            }

            // Tidak bisa menghapus diri sendiri
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus diri sendiri'
                ], 403);
            }

            // Delete all chat histories first
            $user->chatHistories()->delete();
            
            // Delete user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User dan semua chat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user'
            ], 500);
        }
    }

    public function deleteUserChats(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->chatHistories()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua chat user berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus chat user'
            ], 500);
        }
    }
}