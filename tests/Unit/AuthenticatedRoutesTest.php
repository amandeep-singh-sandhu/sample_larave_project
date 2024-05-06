<?php

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;

class AuthenticatedRoutesTest extends TestCase
{
    /**
     * Test if all routes are working with an authenticated user.
     *
     * @return void
     */
    public function testAuthenticatedRoutes()
    {
        // Simulate authentication
        $user = User::factory()->create();
        $this->actingAs($user);

        // Define routes to test
        $routes = [
            '/dashboard',
            '/profile',
            // Add more routes here
        ];

        // Make HTTP requests to each route
        foreach ($routes as $route) {
            $user = User::factory()->create();

            $response = $this
            ->actingAs($user)
            ->get('/profile');

            // Assert response
            $response->assertStatus(200); // Assuming 200 is the expected status for authenticated routes
        }
    }
}
