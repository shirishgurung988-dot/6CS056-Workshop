<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseWebCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_pages_use_the_shared_layout_and_named_routes(): void
    {
        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Training Institute')
            ->assertSee('Students')
            ->assertSee('No courses found.');

        $this->get(route('courses.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="difficulty"', false)
            ->assertSee('name="is_active"', false);
    }

    public function test_course_can_be_created_updated_and_deleted(): void
    {
        $courseData = [
            'name' => 'Laravel',
            'description' => 'Build web applications',
            'duration' => 8,
            'fee' => 500,
            'difficulty' => 'Medium',
            'is_active' => 1,
        ];

        $this->post(route('courses.store'), $courseData)
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success');

        $course = Course::where('name', 'Laravel')->firstOrFail();

        $this->get(route('courses.show', $course))
            ->assertOk()
            ->assertSee('Build web applications');

        $this->put(route('courses.update', $course), [
            ...$courseData,
            'name' => 'Advanced Laravel',
        ])
            ->assertRedirect(route('courses.show', $course))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'name' => 'Advanced Laravel',
        ]);

        $this->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    public function test_course_store_validates_required_fields(): void
    {
        $this->from(route('courses.create'))
            ->post(route('courses.store'), [])
            ->assertRedirect(route('courses.create'))
            ->assertSessionHasErrors(['name', 'duration', 'fee', 'difficulty']);
    }
}
