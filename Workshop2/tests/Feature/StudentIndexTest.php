<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_students_index_displays_the_student_list(): void
    {
        $response = $this->get(route('students.index'));

        $response
            ->assertOk()
            ->assertSee('Students')
            ->assertSee('No students found.');
    }
}
