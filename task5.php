<?php

// A) Explanation of the scheduled command in Laravel
// This code snippet sets up a scheduled task using Laravel's Task Scheduling feature.
Schedule::command('app:example-command') // Specifies the command to be scheduled.
->withoutOverlapping() // Prevents multiple instances of the command from running at the same time.
->hourly() // Schedules the command to run once every hour.
->onOneServer() // Ensures the command runs on only one server in a multi-server environment.
->runInBackground(); // Executes the command in the background, allowing the application to continue processing.

// B) Explanation of Context and Cache Facades
// The Context facade typically refers to the current state or environment of the application,
// while the Cache facade provides an interface to interact with the caching system.

use Illuminate\Support\Facades\Cache;

// Example of using the Cache facade to store and retrieve cached data
Cache::put('key', 'value', 60); // Store 'value' under 'key' for 60 minutes
$value = Cache::get('key'); // Retrieve the cached value

// C) Differences between $query->update(), $model->update(), and $model->updateQuietly()
use App\Models\User;

// $query->update() - Updates records in the database without retrieving model instances
DB::table('users')->where('status', 'inactive')->update(['status' => 'active']);

// $model->update() - Updates a model instance and triggers events
$user = User::find(1);
$user->update(['status' => 'active']);

// $model->updateQuietly() - Updates a model instance without triggering events
$user->updateQuietly(['status' => 'active']);

// Summary of differences
echo "Summary of update methods:\n";
echo "\$query->update(): Used for bulk updates without events.\n";
echo "\$model->update(): Used for updating an instance with event triggers.\n";
echo "\$model->updateQuietly(): Used for updating an instance without triggering events.\n";

?>