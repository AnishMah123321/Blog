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
        $bearer = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMWEwMTdhNmM0NTZmZDFkOWU1MGJmYTAzNzg1NDA1NGJiYjFkZTRkZjBlYTMxYzcwNTZlYTZiMjZhNDFmMzk5ZDdjMDQwMTUwZmU5ZDYyOWIiLCJpYXQiOjE2ODA0ODQyOTEuNzc5NjMzLCJuYmYiOjE2ODA0ODQyOTEuNzc5NjM2LCJleHAiOjE2OTYyOTU0OTEuNjAyMiwic3ViIjoiNCIsInNjb3BlcyI6W119.VDoipuvxq6RxT1h3mOTVexdTtgB5MttsaAIVK5ODLM7g80P4BGZjQM9JQcBKn-9NpLpSzQIQYNtrh2o73pvb7MMMtnxLDFKJYFtKh57QeuOIS_QzvxpKk5fPJ5eiz_HVjQ_0zQMzPe4MJP-JBBfTjRZTULVtXNJHnlyzr2iBbmFubgrRUlox-WBT13NPZsqwodUz1zAEeP8cEgfh6Xv4jlvQzxSUdveYTxHMN04fzR-lK_jO-OZXtlSUmkHFlGPBr5GJuscoBUalHOkUXYqUxP7kDKf8-Fb3xVCvw-RChdSSkfmZCnnbQBDuq04uYdGPnKstCDimzOqYuVJNKTjPKoNsdknCzzicPtDH8XPwNGdHn1v4lfrqKgtxcvWGnxbek-vf1g6z5f3OrG9Htqhg7iXDA96bDlB0g7TDbrgAzQoQDrPkg-fyrvhPpiVe2nGHdg6TRRwFZpcFFINRNFAmvYTRFXz5EVeNdfsatDH_49YiZmJC_0PrrpOdnBhAxo5nLbwVQ7Ow5k5V88RW2qz1BZz41w6WIsHdZ5QS5dv2juhFXeLL4JFkwidpGW8Tjp3zA4ch_Gun0w1Lm75uwZXNab5Z8PiC7FDRsx5Tr-RYXtWV5MzVnPBYS9qppCi8dwIX1u3-LNs_ViBvAiNMjEDiNfttDPPsq7_ycPXNFn0rpQY";
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
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'mobile_number' => mt_rand(1000000000, 9999999999),
            'password' =>  'test',
            'password_confirmation' => 'test'
        ]);
        $response->assertRedirect('/');
    }
}