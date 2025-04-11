<?php

namespace Database\Seeders;

use App\Models\CV;
use App\Models\PreviousJob;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class CVSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        CV::factory()->create([
            'user_id' => $user->id,
            'userName' => 'johndoe',
            'image' => 'profile.jpg',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phoneNumber' => '123456789',
            'email' => 'john@example.com',
            'country' => 'Hungary',
            'city' => 'Budapest',
            'jobctle' => 'Software Engineer',
            'introduce' => 'I am a passionate developer...',
            'age' => 30,
            'ethnic' => 'Caucasian',
        ]);

        $cvId = CV::first();

        PreviousJob::factory()->create([
            'cv_id' => $cvId->id,
            'employer' => 'Google',
            'jobTitle' => 'SoftwareEnginer',
            'startDate' => '2021-01-01',
            'endDate' => '2022-01-01',
            'description' => 'Jó volt',
            'city' => 'Seattle',
        ]);

        Skill::factory()->create([
            'cv_id' => $cvId->id,
            'skillName' => 'Laravel',
            'skillLevel' => 4,
        ]);

    }
}
