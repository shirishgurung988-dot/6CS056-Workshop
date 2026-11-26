<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_students(): void
    {
        Student::create([
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '123456789',
        ]);

        $this->getJson('/api/students')
            ->assertOk()
            ->assertJsonFragment(['email' => 'ada@example.com']);
    }

    public function test_it_creates_a_student(): void
    {
        $this->postJson('/api/students', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '123456789',
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Ada Lovelace');

        $this->assertDatabaseHas('students', ['email' => 'ada@example.com']);
    }

    public function test_it_validates_student_input(): void
    {
        $this->postJson('/api/students', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'phone']);
    }
}
