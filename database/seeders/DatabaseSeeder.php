<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Spatie\Tags\Tag;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tags = [
            'Vacuuming',
            'Errands',
            'Other',
        ];
        foreach ($tags as $tag) {
            Tag::findOrCreate(['name' => $tag]);
        }

        $tasks = [
            'Back landing',
            'Basement stairs',
            'L-Split',
            'Kitchen',
            'Living Room',
            'Dining Room',
            'Office',
            'Bathroom',
            'Laundry Room',
            'Our bathroom',
            'Bedroom Rug - My side',
            'Bedroom Rug - Both sides',
        ];
        foreach ($tasks as $task) {
            Task::factory()->create([
                'name' => $task,
            ]);
        }
    }
}
