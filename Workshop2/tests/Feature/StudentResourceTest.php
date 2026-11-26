<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_be_created_updated_viewed_and_deleted(): void
    {
        $this->get(route('students.create'))
            ->assertOk()
            ->assertSee('name="date_of_birth"', false);

        $studentData = [
            'name' => 'Ada Student',
            'email' => 'ada@example.test',
            'phone' => '555-0101',
            'address' => '1 Example Street',
            'date_of_birth' => '2000-01-15',
        ];

        $this->post(route('students.store'), $studentData)
            ->assertRedirectToRoute('students.index');

        $this->assertDatabaseHas('students', [
            'email' => 'ada@example.test',
            'name' => 'Ada Student',
        ]);

        $student = Student::where('email', 'ada@example.test')->firstOrFail();

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Ada Student');

        $this->get(route('students.edit', $student))
            ->assertOk()
            ->assertSee('name="date_of_birth"', false);

        $this->put(route('students.update', $student), [
            ...$studentData,
            'name' => 'Ada Updated',
        ])->assertRedirectToRoute('students.show', $student);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Ada Updated',
        ]);

        $this->delete(route('students.destroy', $student))
            ->assertRedirectToRoute('students.index');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_cannot_be_created_without_a_date_of_birth(): void
    {
        $this->post(route('students.store'), [
            'name' => 'Ada Student',
            'email' => 'ada@example.test',
            'phone' => '555-0101',
        ])->assertRedirectBackWithErrors(['date_of_birth']);

        $this->assertDatabaseCount('students', 0);
    }
}
