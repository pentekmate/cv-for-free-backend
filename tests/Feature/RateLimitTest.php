<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_rate_limit_exceeded()
    {
        RateLimiter::clear('createCv');

        $payload = [
            'data' => [
                'user_id' => 1,
                'userName' => 'johndoe',
                'image' => 'profile.jpg',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'phoneNumber' => '123456789',
                'email' => 'john@example.com',
                'country' => 'Hungary',
                'city' => 'Budapest',
                'jobTitle' => 'Software Engineer',
                'introduce' => 'I am a passionate developer...',
                'age' => 30,
                'ethnic' => 'Caucasian',
            ],
            'previousJobs' => [
                [
                    'employer' => 'Google',
                    'jobTitle' => 'Software Engineer',
                    'startDate' => '2021-01-01',
                    'endDate' => '2022-01-01',
                    'description' => 'Jó volt',
                    'city' => 'Seattle',
                ],
            ],
            'skills' => [],
        ];

        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson('/api/createCv', $payload);
            $response->assertStatus(200);
        }

        $response = $this->postJson('/api/createCv', $payload);
        $response->assertStatus(429); // 429 = Too Many Requests
        $response->assertJson(['message' => 'Too Many Attempts.']);
    }
}
