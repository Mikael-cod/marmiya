<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SecurityLoginAttempt extends Model
{
    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'user_agent',
        'attempted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    public static function record(string $email, string $ipAddress, bool $successful, ?string $userAgent = null): self
    {
        return static::query()->create([
            'email' => Str::limit($email, 255),
            'ip_address' => Str::limit($ipAddress, 45),
            'successful' => $successful,
            'user_agent' => $userAgent ? Str::limit($userAgent, 500) : null,
            'attempted_at' => now(),
        ]);
    }

    /**
     * @param  Builder<SecurityLoginAttempt>  $query
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('successful', false);
    }
}
