<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_courses(): void
    {
        Course::create([
            'name' => 'Laravel',
            'description' => 'Build APIs',
            'duration' => 8,
            'fee' => 500,
            'difficulty' => 'Medium',
            'is_active' => true,
        ]);

        $this->getJson('/api/courses')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Laravel']);
    }

    public function test_it_creates_a_course(): void
    {
        $this->postJson('/api/courses', [
            'name' => 'Laravel',
            'description' => 'Build APIs',
            'duration' => 8,
            'fee' => 500,
            'difficulty' => 'Medium',
            'is_active' => true,
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Laravel');

        $this->assertDatabaseHas('courses', ['name' => 'Laravel']);
    }

    public function test_it_validates_course_input(): void
    {
        $this->postJson('/api/courses', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'duration', 'fee', 'difficulty']);
    }
}
