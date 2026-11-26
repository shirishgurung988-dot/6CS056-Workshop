<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_can_be_created_updated_viewed_and_deleted(): void
    {
        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('No courses found.');

        $this->get(route('courses.create'))
            ->assertOk()
            ->assertSee('name="difficulty"', false);

        $courseData = [
            'name' => 'Web Development',
            'description' => 'An introductory course.',
            'duration' => 12,
            'fee' => '250.00',
            'difficulty' => 'Easy',
            'is_active' => '1',
        ];

        $this->post(route('courses.store'), $courseData)
            ->assertRedirectToRoute('courses.index');

        $this->assertDatabaseHas('courses', [
            'name' => 'Web Development',
            'is_active' => 1,
        ]);

        $course = Course::where('name', 'Web Development')->firstOrFail();

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Web Development');

        $this->get(route('courses.edit', $course))
            ->assertOk()
            ->assertSee('name="difficulty"', false);

        $this->put(route('courses.update', $course), [
            ...$courseData,
            'name' => 'Updated Web Development',
            'is_active' => '0',
        ])->assertRedirectToRoute('courses.show', $course);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Updated Web Development',
            'is_active' => 0,
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirectToRoute('courses.index');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }
}
