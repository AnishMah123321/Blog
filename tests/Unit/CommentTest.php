<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;


class CommentTest extends TestCase
{

    use WithFaker;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_store_comment_with_login()
    {
        auth()->attempt(array(
            'email' => 'unittest@user.test',
            'password' => 'test',
        ));
        $bearer = auth()->user()->createToken('authToken')->accessToken;

        $data = [
            'customer_id' => '1',
            'category_id' => '1',
            'title' => 'For Test Comment',
            'post' => 'For Test Comment',
        ];
        $post = Post::create($data);

        $response = $this->withHeader('Authorization', 'Bearer ' . $bearer)->post('/api/save-comment',[
            'customer_id' => '1',
            'post_id' => $post->id,
            'comment' => 'TEST COMMENT',
        ]);
        $response->assertStatus(200);
    }

    public function test_store_comment_without_login()
    {
        $response = $this->post('/api/save-post',[
            'customer_id' => '1',
            'post_id' => '1',
            'comment' => 'TEST COMMENT',
        ]);
        $response->assertRedirect('/');
    }
}