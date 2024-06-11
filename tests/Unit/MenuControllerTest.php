<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Menu;
use Mockery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class MenuControllerTest extends TestCase
{
    public function testIndexReturnsAllMenus()
    {
        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('all')->once()->andReturn(collect([
            ['name' => 'Pizza', 'description' => 'Delicious pizza', 'price' => 10.0, 'quantity' => 5, 'image' => 'image_url'],
            ['name' => 'Pasta', 'description' => 'Delicious pasta', 'price' => 8.0, 'quantity' => 10, 'image' => 'image_url']
        ]));

        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function testStoreValidationFails()
    {
        $data = [
            'name' => '',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ];

        // Mocking request validation
        $request = Request::create('/menu', 'POST', $data);
        $request->setMethod('POST');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'required|string'
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testShowReturnsMenuItem()
    {
        // Mocking the Menu model
        $menu = [
            'name' => 'Margherita',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ];

        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('find')->once()->with(1)->andReturn($menu);

        $response = $this->get('/menu/1');

        $response->assertStatus(200);
        $response->assertJson($menu);
    }

    public function testShowReturns404IfMenuItemNotFound()
    {
        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('find')->once()->with(999)->andReturn(null);

        $response = $this->get('/menu/999');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Menu item not found']);
    }

    public function testUpdateReturns404IfMenuItemNotFound()
    {
        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'price' => 9.99,
            'quantity' => 5,
            'image' => 'updated_image_url'
        ];

        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('find')->once()->with(999)->andReturn(null);

        $response = $this->putJson('/menu/999', $data);

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Menu item not found']);
    }

    public function testDestroySuccessfullyDeletesMenuItem()
    {
        // Mocking the Menu model
        $menu = Mockery::mock('alias:App\Models\Menu');
        $menu->shouldReceive('find')->once()->with(1)->andReturn($menu);
        $menu->shouldReceive('delete')->once()->andReturn(true);

        $response = $this->delete('/menu/1');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Menu item deleted']);
    }

    public function testDestroyReturns404IfMenuItemNotFound()
    {
        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('find')->once()->with(999)->andReturn(null);

        $response = $this->delete('/menu/999');

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Menu item not found']);
    }

    public function testUpdateValidationFails()
    {
        $menu = [
            'name' => 'Margherita',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ];

        $data = [
            'name' => '',
            'description' => 'Updated description',
            'price' => 9.99,
            'quantity' => 5,
            'image' => 'updated_image_url'
        ];

        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('find')->once()->with(1)->andReturn($menu);

        // Mocking request validation
        $request = Request::create('/menu/1', 'PUT', $data);
        $request->setMethod('PUT');
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'required|string'
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function testUpdateSuccessfullyUpdatesMenuItem()
    {
        // Mocking the Menu model
        $menu = Mockery::mock('alias:App\Models\Menu');
        $menu->shouldReceive('find')->once()->with(1)->andReturn($menu);
        $menu->shouldReceive('update')->once()->with([
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'price' => 9.99,
            'quantity' => 5,
            'image' => 'updated_image_url'
        ])->andReturn(true);

        $data = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'price' => 9.99,
            'quantity' => 5,
            'image' => 'updated_image_url'
        ];

        $response = $this->putJson('/menu/1', $data);

        $response->assertStatus(200);
        $response->assertJson($data);
    }

    public function testStoreCreatesNewMenuItem()
    {
        // Mocking the Menu model
        $mock = Mockery::mock('alias:App\Models\Menu');
        $mock->shouldReceive('create')->once()->with([
            'name' => 'Margherita',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ])->andReturn((object)[
            'name' => 'Margherita',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ]);

        $data = [
            'name' => 'Margherita',
            'description' => 'Classic pizza with cheese and tomato',
            'price' => 8.99,
            'quantity' => 10,
            'image' => 'image_url'
        ];

        $response = $this->postJson('/menu', $data);

        $response->assertStatus(201);
        $response->assertJson($data);
    }
}
