<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_make_admin_command_creates_user(): void
    {
        $this->artisan('make:admin', [
            '--name' => 'Agent Victor',
            '--email' => 'victor@example.com',
            '--password' => 'secret12345',
        ])
        ->expectsOutput("Admin user 'Agent Victor' (victor@example.com) successfully created/updated.")
        ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'victor@example.com',
            'name' => 'Agent Victor',
        ]);

        $user = User::where('email', 'victor@example.com')->first();
        $this->assertTrue(Hash::check('secret12345', $user->password));
    }

    public function test_make_admin_command_updates_existing_admin(): void
    {
        User::factory()->create([
            'email' => 'victor@example.com',
            'name' => 'Old Name',
        ]);

        $this->artisan('make:admin', [
            '--name' => 'Updated Name',
            '--email' => 'victor@example.com',
            '--password' => 'newpassword123',
        ])
        ->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'email' => 'victor@example.com',
            'name' => 'Updated Name',
        ]);

        $user = User::where('email', 'victor@example.com')->first();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_make_admin_command_fails_with_invalid_email(): void
    {
        $this->artisan('make:admin', [
            '--name' => 'Admin',
            '--email' => 'not-an-email',
            '--password' => 'secret12345',
        ])
        ->assertFailed();
    }
}
