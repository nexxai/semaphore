<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DayTask extends Model
{
    /** @use HasFactory<\Database\Factories\DayTaskFactory> */
    use HasFactory;

    protected $table = 'day_task';

    protected $fillable = [
        'day_id',
        'task_id',
        'completed',
        'description',
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Check if this DayTask can be marked as complete.
     * A DayTask can only be completed if all its subtasks are completed.
     */
    public function canBeCompleted(): bool
    {
        if ($this->subtasks->isEmpty()) {
            return true;
        }

        return $this->subtasks->every(fn (Subtask $subtask) => $subtask->completed);
    }

    /**
     * Mark this DayTask as completed, but only if all subtasks are completed.
     */
    public function markCompleted(): bool
    {
        if (! $this->canBeCompleted()) {
            return false;
        }

        $this->update(['completed' => true]);

        return true;
    }
}
