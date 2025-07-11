<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Tags\HasTags;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    use HasTags;

    protected $fillable = [
        'name',
        'day_id',
    ];

    protected $casts = [
        'day_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class);
    }

    /**
     * Since a task can have multiple tags, we want to return a collection of tasks grouped by their tag.
     * That means that a single task can belong to multiple categories.
     * For example, a task can be tagged as 'work' and 'personal', and we want to group it under both categories.
     */
    public static function groupByCategories(): Collection
    {
        $tasks = static::with('tags')->get();
        $categories = collect([]);

        foreach ($tasks as $task) {
            foreach ($task->tags as $tag) {
                if (! $categories->has($tag->name)) {
                    $categories[$tag->name] = collect();
                }
                $categories[$tag->name]->push($task);
            }
        }

        return $categories;
    }
}
