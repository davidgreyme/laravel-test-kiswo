<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;
use App\Models\User;

class AuthTest extends TestCase
{
    /** @test */
    public function register_displays_the_register_form()
    {
        $response = $this->get(route('register'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    public function register_displays_required_errors()
    {
        $response = $this->post('/register', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'username', 'password']);
    }

    /** @test */
    public function register_displays_duplicated_username_error()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        $form_data = array(
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        );
        $response = $this->post('/register', $form_data);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'username']);
    }

    /** @test */
    public function register_creates_and_authenticates_a_user()
    {
        $user = User::factory()->make();
        $register_info = array(
            'username' => $user->username,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        );

        $response = $this->post('register', $register_info);

        $response->assertRedirect(route('home'));
        $user = User::where('email', $register_info['email'])->where('username', $register_info['username'])->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_displays_the_login_form()
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function login_displays_validation_errors()
    {
        $response = $this->post('/login', []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function login_authenticates_and_redirects_user()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }
}
