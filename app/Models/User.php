<?php
// app/Models/User.php (Fixed)
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function chatHistories(): HasMany
    {
        return $this->hasMany(ChatHistory::class);
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is banned
    public function isBanned()
    {
        return $this->status === 'banned';
    }

    // Get chat sessions yang unik untuk sidebar
    public function getChatSessions($limit = 50)
    {
        return $this->chatHistories()
            ->select('session_id', 'chat_title', 'user_message', 'created_at')
            ->whereNotNull('session_id')
            ->groupBy('session_id', 'chat_title', 'user_message', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->unique('session_id')
            ->take($limit);
    }

    // Get specific chat session dengan semua message
    public function getChatBySession($sessionId)
    {
        return $this->chatHistories()
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // Get chat history dengan limit
    public function getRecentChats($limit = 50)
    {
        return $this->chatHistories()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}