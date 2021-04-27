<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class UserTest extends TestCase
{
    /** @test */
    public function update_displays()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        Auth::login($user);
        $response = $this->get(route('user.edit', ['user' => $user->id]));
        $response->assertViewIs('user.edit');
        $response->assertStatus(200);
    }

    /** @test */
    public function update_another_user_info()
    {
        $users = User::all();
        $user_ary = array();
        $user_count = count($users);
        foreach ($users as $user) {
            array_push($user_ary, $user);
        }
        if ($user_count < 2) {
            while ($user_count < 2) {
                $user = User::factory()->create();
                array_push($user_ary, $user);
                $user_count++;
            }
        }
        $current_user = $user_ary[0];
        $target_user = $user_ary[1];
        Auth::login($current_user);
        $response = $this->put(route('user.update', ['user' => $target_user]), [
            'username' => 'update_test',
            'email' => $target_user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function delete_another_user()
    {
        $users = User::all();
        $user_ary = array();
        $user_count = count($users);
        foreach ($users as $user) {
            array_push($user_ary, $user);
        }
        if ($user_count < 2) {
            while ($user_count < 2) {
                $user = User::factory()->create();
                array_push($user_ary, $user);
                $user_count++;
            }
        }
        $current_user = $user_ary[0];
        $target_user = $user_ary[1];
        Auth::login($current_user);
        $response = $this->delete(route('user.destroy', ['user' => $target_user]));
        $response->assertStatus(403);
    }

    /** @test */
    public function list_displays()
    {
        $users = User::all();
        if (count($users) < 10)
            User::factory()->count(10)->create();
        $products = Product::all();
        if (count($products) < 100)
            Product::factory()->count(100)->create();

        $response = $this->get(route('user.list'));
        $response->assertViewIs('user.list');
        $response->assertStatus(200);
        $content = $response->getOriginalContent()->getData();
        $product_count = count($content['items']);
        $this->assertTrue($product_count <= 5);
    }
}
