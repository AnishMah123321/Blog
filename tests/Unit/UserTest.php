<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class UserTest extends TestCase
{

    use WithFaker;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    
    public function test_create_new_users()
    {
        $response = $this->post('/api/register',[
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => mt_rand(1000000000, 9999999999),
            'password' =>  'test',
            'password_confirmation' => 'test'
        ]);
        $response->assertStatus(200);
    }

    public function test_login()
    {
        $response = $this->post('/api/login',[
            'email' => 'unittest@user.test',
            'passowrd' => 'test',
        ]);
        $response->assertStatus(200);
    }
}