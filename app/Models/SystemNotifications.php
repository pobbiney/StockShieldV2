<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SystemNotifications extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'link',
        'type',
        'reference_id',
        'read_at',
        'action_route',
        'action_url',
        'store_id',
        'created_by',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeUnreadForUser(Builder $query, int $userId): Builder
    {
        return $query->forUser($userId)->unread();
    }

    public function markRead(): bool
    {
        if ($this->read_at) {
            return true;
        }

        return $this->update(['read_at' => now()]);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
