<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class PostTest extends TestCase
{

    use WithFaker;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_store_post_with_login()
    {
        auth()->attempt(array(
            'email' => 'unittest@user.test',
            'password' => 'test',
        ));
        $bearer = auth()->user()->createToken('authToken')->accessToken;
        $response = $this->withHeader('Authorization', 'Bearer ' . $bearer)->post('/api/save-post',[
            'post' => 'TEST POST',
            'category_id' => '1',
            'title' => 'TEST TITLE',
        ]);
        $response->assertStatus(200);
    }

    public function test_store_post_without_login()
    {
        $response = $this->post('/api/save-post',[
            'post' => 'TEST POST',
            'category_id' => '1',
            'title' => 'TEST TITLE',
        ]);
        $response->assertRedirect('/');
    }
}