<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function tasks(): BelongsToMany
    {
        return $this->BelongsToMany(Task::class)->withPivot(['completed', 'description']);
    }

    /**
     * Only return the tasks where the pivot table's completed column is true.
     */
    public function incompleteTasks(): BelongsToMany
    {
        return $this->BelongsToMany(Task::class)->wherePivot('completed', false);
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
            return $day->load('tasks');
        }

        // If all tasks are completed, create a new day
        return static::create([
            'date' => now(),
        ]);
    }
}
