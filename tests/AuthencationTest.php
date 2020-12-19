<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    /**
     * test login.
     *
     * @return void
     */
    public function testLogin()
    {
        $response = $this->json('POST', 'api/login', [
            'email' => 'admin.user@gp-hackathon.com', 
            'password' => 'password123'
        ], []);
        $this->assertEquals(200, $this->response->status());
    }

    /**
     * test register.
     *
     * @return void
     */
    public function testRegister()
    {
        $response = $this->json('POST', 'api/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '00001111000',
            'role' => 'user',
            'email' => 'test.user@gp-hackathon.com',
            'password' => Hash::make('password123'),
        ], []);
        $this->assertEquals(200, $this->response->status());
    }
}
