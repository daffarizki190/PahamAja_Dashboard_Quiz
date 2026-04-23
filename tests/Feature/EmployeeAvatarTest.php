<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_employee_avatar()
    {
        Storage::fake('public');

        $avatar = UploadedFile::fake()->image('avatar.jpg');
        
        $employeeData = [
            'name' => 'John Doe',
            'nim' => 'JD-' . time(),
            'department' => 'IT',
            'position' => 'Developer',
            'avatar' => $avatar,
        ];

        $response = $this->post(route('admin.employees.store'), $employeeData);

        $response->assertRedirect();
        
        $employee = Employee::where('nim', $employeeData['nim'])->first();
        $this->assertNotNull($employee->avatar);
        Storage::disk('public')->assertExists($employee->avatar);
    }

    public function test_can_update_employee_avatar()
    {
        Storage::fake('public');

        $employee = Employee::create([
            'name' => 'Test Employee',
            'nim' => 'TEST-123',
            'department' => 'IT',
            'position' => 'Staff',
            'status' => 'Active',
        ]);

        $newAvatar = UploadedFile::fake()->image('new-avatar.jpg');

        $response = $this->post(route('admin.employees.update', $employee->id), [
            '_method' => 'PUT',
            'name' => 'John Updated',
            'department' => $employee->department,
            'position' => $employee->position,
            'status' => 'Active',
            'avatar' => $newAvatar,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $employee->refresh();
        
        $this->assertEquals('John Updated', $employee->name);
        $this->assertNotNull($employee->avatar);
        Storage::disk('public')->assertExists($employee->avatar);
    }
}
