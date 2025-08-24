<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = require_once database_path('/data/activities.php');
        $this->addActivities($activities);
    }

    private function addActivities($items, $parentId = null): void
    {
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $parentActivity = Activity::query()->create([
                    'name' => $key,
                    'parent_id' => $parentId
                ]);

                $randomOrganizationsIds = Organization::query()->inRandomOrder()->limit(rand(1, 3))->get()->pluck('id');
                $parentActivity->organizations()->syncWithoutDetaching($randomOrganizationsIds);

                $this->addActivities($value, $parentActivity->id);
            } else {
                $activity = Activity::query()->create([
                    'name' => $value,
                    'parent_id' => $parentId
                ]);

                $randomOrganizationsIds = Organization::query()->inRandomOrder()->limit(rand(1, 3))->get()->pluck('id');
                $activity->organizations()->syncWithoutDetaching($randomOrganizationsIds);

            }
        }
    }
}
