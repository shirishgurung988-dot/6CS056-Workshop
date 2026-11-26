<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWebCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_pages_use_the_shared_layout_and_named_routes(): void
    {
        $this->get(route('students.index'))
            ->assertOk()
            ->assertSee('Training Institute')
            ->assertSee('Courses')
            ->assertSee('No students found.');

        $this->get(route('students.create'))
            ->assertOk()
            ->assertSee('name="name"', false)
            ->assertSee('name="date_of_birth"', false);
    }

    public function test_student_can_be_created_updated_and_deleted(): void
    {
        $studentData = [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '123456789',
            'address' => 'London',
            'date_of_birth' => '1815-12-10',
        ];

        $this->post(route('students.store'), $studentData)
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success');

        $student = Student::where('email', $studentData['email'])->firstOrFail();

        $this->get(route('students.show', $student))
            ->assertOk()
            ->assertSee('Age:')
            ->assertSee('Ada Lovelace');

        $this->put(route('students.update', $student), [
            ...$studentData,
            'name' => 'Ada Byron',
        ])
            ->assertRedirect(route('students.show', $student))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Ada Byron',
            'email' => $studentData['email'],
        ]);

        $this->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_student_store_rejects_missing_fields_and_duplicate_emails(): void
    {
        $this->from(route('students.create'))
            ->post(route('students.store'), [])
            ->assertRedirect(route('students.create'))
            ->assertSessionHasErrors(['name', 'email', 'phone']);

        Student::create([
            'name' => 'Existing Student',
            'email' => 'existing@example.com',
            'phone' => '123456789',
        ]);

        $this->from(route('students.create'))
            ->post(route('students.store'), [
                'name' => 'Another Student',
                'email' => 'existing@example.com',
                'phone' => '987654321',
            ])
            ->assertRedirect(route('students.create'))
            ->assertSessionHasErrors('email');
    }
}
