<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function index()
    {
        $this->markUserOnline();
        $currentUser = Auth::user();
        
        // Check if current user is a supplier with restricted access
        $isRestrictedSupplier = false;
        $vendor = null;
        
        if ($currentUser->role === 'supplier') {
            $vendor = Vendor::where('user_id', $currentUser->id)->first();
            if ($vendor && !$vendor->hasFullAccess()) {
                $isRestrictedSupplier = true;
            }
        }
        
        // If supplier has restricted access, only show admins
        if ($isRestrictedSupplier) {
            $admins = User::where('role', 'admin')
                ->where('id', '!=', $currentUser->id)
                ->where('status', 'active')
                ->orderBy('username', 'asc')
                ->get();
                
            $totalContacts = $admins->count();
            
            return view('chat.index', compact(
                'admins',
                'totalContacts',
                'isRestrictedSupplier',
                'vendor'
            ));
        }
        
        // Full access - show all users
        $farmers = User::where('role', 'farmer')
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('username', 'asc')
            ->get();
            
        $manufacturers = User::where('role', 'manufacturer')
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('username', 'asc')
            ->get();
            
        $admins = User::where('role', 'admin')
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('username', 'asc')
            ->get();
            
        $distributors = User::where('role', 'distributor')
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('username', 'asc')
            ->get();
            
        $retailers = User::where('role', 'retailer')
            ->where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->orderBy('username', 'asc')
            ->get();

        // Get total counts for display
        $totalContacts = $farmers->count() + $manufacturers->count() + $distributors->count() + $retailers->count() + $admins->count();

        return view('chat.index', compact(
            'farmers', 
            'manufacturers', 
            'admins', 
            'distributors', 
            'retailers',
            'totalContacts',
            'isRestrictedSupplier',
            'vendor'
        ));
    }

    /**
     * List users the current user can chat with, based on role
     */
    public function listChatUsers()
    {
        $user = Auth::user();
        $role = $user->role;
        $query = User::query()->where('id', '!=', $user->id)->where('status', 'active');
        $allowedRoles = [];
        switch ($role) {
            case 'admin':
                // Admin can chat with everyone
                return response()->json(User::where('id', '!=', $user->id)->where('status', 'active')->get());
            case 'supplier':
                $allowedRoles = ['farmer', 'manufacturer', 'admin'];
                break;
            case 'manufacturer':
                $allowedRoles = ['supplier', 'admin', 'distributor', 'retailer'];
                break;
            case 'distributor':
                $allowedRoles = ['manufacturer', 'retailer', 'admin'];
                break;
            case 'retailer':
                $allowedRoles = ['manufacturer', 'distributor', 'admin'];
                break;
            case 'farmer':
                $allowedRoles = ['supplier', 'admin'];
                break;
            default:
                $allowedRoles = ['admin'];
        }
        $contacts = $query->whereIn('role', $allowedRoles)->get();
        return response()->json($contacts);
    }

    /**
     * Send a chat message, store in DB, broadcast event, enforce permissions
     */
    public function sendMessage(Request $request)
    {
        $this->markUserOnline();
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);

        $sender = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);

        // Enforce chat permissions
        if (!$this->canChatWith($sender, $recipient)) {
            return response()->json(['error' => 'You are not allowed to chat with this user.'], 403);
        }

        // Store message
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $recipient->id,
            'message' => $request->message,
        ]);

        // Broadcast event
        broadcast(new MessageSent($message))->toOthers();

        // Optionally, send notification (existing logic)
        NotificationService::sendChatNotification($sender, $recipient, $request->message);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }

    /**
     * Fetch messages between authenticated user and another user
     */
    public function fetchMessages($userId)
    {
        $this->markUserOnline();
        $auth = Auth::user();
        $other = User::findOrFail($userId);
        if (!$this->canChatWith($auth, $other)) {
            return response()->json(['error' => 'You are not allowed to chat with this user.'], 403);
        }
        $messages = Message::where(function($q) use ($auth, $other) {
            $q->where('sender_id', $auth->id)->where('receiver_id', $other->id);
        })->orWhere(function($q) use ($auth, $other) {
            $q->where('sender_id', $other->id)->where('receiver_id', $auth->id);
        })->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }

    /**
     * Check if sender can chat with recipient based on role matrix
     */
    private function canChatWith($sender, $recipient)
    {
        if ($sender->role === 'admin') return true;
        $matrix = [
            'supplier' => ['farmer', 'manufacturer', 'admin'],
            'manufacturer' => ['supplier', 'admin', 'distributor', 'retailer'],
            'distributor' => ['manufacturer', 'retailer', 'admin'],
            'retailer' => ['manufacturer', 'distributor', 'admin'],
            'farmer' => ['supplier', 'admin'],
        ];
        return isset($matrix[$sender->role]) && in_array($recipient->role, $matrix[$sender->role]);
    }

    /**
     * Get user details for chat
     */
    public function getUserDetails($userId)
    {
        $this->markUserOnline();
        $currentUser = Auth::user();
        $user = User::where('id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check if supplier is trying to chat with non-admin
        if ($currentUser->role === 'supplier') {
            $vendor = Vendor::where('user_id', $currentUser->id)->first();
            if ($vendor && !$vendor->hasFullAccess() && $user->role !== 'admin') {
                return response()->json(['error' => 'Access restricted. You can only chat with administrators until fully approved.'], 403);
            }
        }

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'address' => $user->address,
            'status' => $user->status,
            'created_at' => $user->created_at->format('M d, Y')
        ]);
    }

    /**
     * Get online users count
     */
    public function getOnlineUsers()
    {
        $currentUser = Auth::user();
        
        // If supplier has restricted access, only count admins
        if ($currentUser->role === 'supplier') {
            $vendor = Vendor::where('user_id', $currentUser->id)->first();
            if ($vendor && !$vendor->hasFullAccess()) {
                $onlineUsers = User::where('role', 'admin')
                    ->where('id', '!=', $currentUser->id)
                    ->where('status', 'active')
                    ->count();
                    
                return response()->json(['online_users' => $onlineUsers]);
            }
        }
        
        // Full access - count all users
        $onlineUsers = User::where('id', '!=', $currentUser->id)
            ->where('status', 'active')
            ->count();

        return response()->json(['online_users' => $onlineUsers]);
    }

    /**
     * Return a list of online user IDs (for polling-based online status)
     */
    public function getOnlineUserIds()
    {
        // We'll use Laravel's cache to store online user IDs
        $onlineUserIds = \Cache::get('online_users', []);
        return response()->json(['online_user_ids' => $onlineUserIds]);
    }

    /**
     * Mark the current user as online (call this on chat page load or message send)
     */
    private function markUserOnline()
    {
        $userId = auth()->id();
        $now = now();
        $onlineUsers = \Cache::get('online_users', []);
        $onlineUsers[$userId] = $now->timestamp;
        // Remove users inactive for >2 minutes
        $onlineUsers = array_filter($onlineUsers, function($lastSeen) use ($now) {
            return $now->timestamp - $lastSeen < 120;
        });
        \Cache::put('online_users', $onlineUsers, 3);
    }

    /**
     * Return unread message counts for the authenticated user, grouped by sender_id
     */
    public function getUnreadCounts()
    {
        $user = auth()->user();
        $counts = 
            \App\Models\Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->groupBy('sender_id')
            ->selectRaw('sender_id, COUNT(*) as count')
            ->pluck('count', 'sender_id');
        return response()->json(['unread_counts' => $counts]);
    }

    /**
     * Set typing status for the authenticated user (called when user is typing to a contact)
     */
    public function setTyping(Request $request)
    {
        $userId = auth()->id();
        $contactId = $request->input('contact_id');
        if (!$contactId) return response()->json(['success' => false]);
        // Store in cache: key = typing_{contactId}, value = array of userIds typing to this contact
        $key = 'typing_' . $contactId;
        $typingUsers = \Cache::get($key, []);
        $typingUsers[$userId] = now()->timestamp;
        // Remove users who stopped typing >5s ago
        $now = now()->timestamp;
        $typingUsers = array_filter($typingUsers, function($ts) use ($now) { return $now - $ts < 5; });
        \Cache::put($key, $typingUsers, 6);
        return response()->json(['success' => true]);
    }

    /**
     * Get typing users for the authenticated user (who is typing to me?)
     */
    public function getTyping()
    {
        $userId = auth()->id();
        $key = 'typing_' . $userId;
        $typingUsers = \Cache::get($key, []);
        return response()->json(['typing_user_ids' => array_keys($typingUsers)]);
    }
} 