<?php

namespace Database\Seeders;

use App\Models\OrganizationPhone;
use Illuminate\Database\Seeder;

class OrganizationPhoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrganizationPhone::factory(250)->create();
    }
}
