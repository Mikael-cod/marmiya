<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InmateFilePage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'inmate_file_record_id',
        'page_number',
        'image_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'page_number' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fileRecord(): BelongsTo
    {
        return $this->belongsTo(InmateFileRecord::class, 'inmate_file_record_id');
    }

    public function imageUrl(): string
    {
        return '/storage/'.ltrim($this->image_path, '/');
    }
}
