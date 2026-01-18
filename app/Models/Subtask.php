<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subtask extends Model
{
    /** @use HasFactory<\Database\Factories\SubtaskFactory> */
    use HasFactory;

    protected $fillable = [
        'day_task_id',
        'name',
        'completed',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function dayTask(): BelongsTo
    {
        return $this->belongsTo(DayTask::class);
    }
}
