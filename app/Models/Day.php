<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    /** @use HasFactory<\Database\Factories\DayFactory> */
    use HasFactory;

    protected $fillable = [
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function dayTasks(): HasMany
    {
        return $this->hasMany(DayTask::class)->with('subtasks');
    }

    /**
     * Get tasks through dayTasks relationship for backward compatibility.
     * This simulates the old belongsToMany relationship.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'day_task')->withPivot(['id', 'completed', 'description']);
    }

    /**
     * Only return the tasks where the DayTask's completed column is false.
     */
    public function incompleteTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'day_task')->wherePivot('completed', false);
    }

    public static function getCurrentDay(): Day
    {
        $day = static::latest()->first();

        if (! $day) {
            return static::create([
                'date' => now(),
            ]);
        }

        if ($day->incompleteTasks()->exists()) {
            // If the last day has unfinished tasks, return it
            return $day->load(['tasks', 'dayTasks.subtasks']);
        }

        // If all tasks are completed, create a new day
        return static::create([
            'date' => now(),
        ])->load(['tasks', 'dayTasks.subtasks']);
    }
}
