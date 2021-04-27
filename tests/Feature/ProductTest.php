<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /** @test */
    public function create_displays()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        Auth::login($user);
        $response = $this->get(route('product.create'));
        $this->assertAuthenticatedAs($user);
        $response->assertViewIs('product.create');
        $response->assertStatus(200);
    }

    /** @test */
    public function create_displays_validation_errors()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        Auth::login($user);
        $response = $this->post(route('product.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title', 'price']);
    }

    /** @test */
    public function edit_displays()
    {
        $user = User::first();
        if (empty($user)) {
            $user = User::factory()->create();
        }
        $product = Product::first();
        if (empty($product)) {
            $product = Product::factory(['user_id' => $user->id])->create();
        }
        Auth::login($user);
        $response = $this->get(route('product.edit', ['product' => $product]));
        $this->assertAuthenticatedAs($user);
        $response->assertViewIs('product.edit');
        $response->assertStatus(200);
    }

    /** @test */
    public function update_product_in_another_user()
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
        $product = Product::where('user_id', $target_user->id)->first();
        if (empty($product)) {
            $product = Product::factory(['user_id' => $target_user->id])->create();
        }
        Auth::login($current_user);
        $response = $this->put(route('product.update', ['product' => $product]), [
            'title' => 'Update Product Test',
            'price' => 200
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function delete_product_in_another_user()
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
        $product = Product::where('user_id', $target_user->id)->first();
        if (empty($product)) {
            $product = Product::factory(['user_id' => $target_user->id])->create();
        }
        Auth::login($current_user);
        $response = $this->delete(route('product.destroy', ['product' => $product]));
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

        $response = $this->get(route('product.list'));
        $response->assertViewIs('product.list');
        $response->assertStatus(200);
        $content = $response->getOriginalContent()->getData();
        $product_count = count($content['products']);
        $this->assertTrue($product_count <= 5);
    }
}
