<?php

declare(strict_types=1);

namespace Snicco\Middleware\GuestsOnly\Tests;

use Snicco\Component\HttpRouting\Routing\Route\Route;
use Snicco\Component\HttpRouting\Testing\MiddlewareTestCase;
use Snicco\Component\ScopableWP\ScopableWP;
use Snicco\Middleware\GuestsOnly\GuestsOnly;

use function json_encode;

use const JSON_THROW_ON_ERROR;

class GuestsOnlyTest extends MiddlewareTestCase
{

    /**
     * @test
     */
    public function guests_can_access_the_route(): void
    {
        $wp = new GuestScopableWP(false);

        $response = $this->runMiddleware($this->newMiddleware($wp), $this->frontendRequest());

        $response->assertNextMiddlewareCalled();
        $response->psr()->assertOk();
    }

    /**
     * @test
     */
    public function logged_in_users_are_redirected_to_a_dashboard_route_if_it_exists(): void
    {
        $route = Route::create('/dashboard', Route::DELEGATE, 'dashboard');
        $this->withRoutes([$route]);

        $wp = new GuestScopableWP(true);

        $response = $this->runMiddleware($this->newMiddleware($wp), $this->frontendRequest());

        $response->psr()->assertRedirect('/dashboard');
        $response->assertNextMiddlewareNotCalled();
    }

    /**
     * @test
     */
    public function logged_in_users_are_redirected_to_a_home_route_if_it_exists_and_no_dashboard_route_exists(): void
    {
        $route = Route::create('/home', Route::DELEGATE, 'home');
        $this->withRoutes([$route]);

        $wp = new GuestScopableWP(true);

        $response = $this->runMiddleware($this->newMiddleware($wp), $this->frontendRequest());

        $response->psr()->assertRedirect('/home');
        $response->assertNextMiddlewareNotCalled();
    }

    /**
     * @test
     */
    public function if_no_route_exists_users_are_redirected_to_the_root_domain_path(): void
    {
        $wp = new GuestScopableWP(true);

        $response = $this->runMiddleware($this->newMiddleware($wp), $this->frontendRequest());

        $response->psr()->assertRedirect('/');
        $response->assertNextMiddlewareNotCalled();
    }

    /**
     * @test
     */
    public function logged_in_users_can_be_redirected_to_custom_urls(): void
    {
        $response = $this->runMiddleware(
            $this->newMiddleware(new GuestScopableWP(true), '/custom-home-page'),
            $this->frontendRequest()
        );

        $response->psr()->assertRedirect('/custom-home-page');
        $response->assertNextMiddlewareNotCalled();
    }

    /**
     * @test
     */
    public function a_json_response_is_returned_if_the_request_wants_json(): void
    {
        $response = $this->runMiddleware(
            $this->newMiddleware(new GuestScopableWP(true)),
            $this->frontendRequest()->withHeader('Accept', 'application/json')
        );

        $response->assertNextMiddlewareNotCalled();
        $psr_response = $response->psr();
        $psr_response->assertIsJson();
        $psr_response->assertBodyExact(
            json_encode(['message' => 'You are already authenticated'], JSON_THROW_ON_ERROR)
        );
        $psr_response->assertForbidden();
    }

    /**
     * @test
     */
    public function a_custom_json_failure_message_can_be_used(): void
    {
        $response = $this->runMiddleware(
            $this->newMiddleware(new GuestScopableWP(true), null, 'Guests only'),
            $this->frontendRequest()->withHeader('Accept', 'application/json')
        );

        $response->assertNextMiddlewareNotCalled();
        $psr_response = $response->psr();
        $psr_response->assertIsJson();
        $psr_response->assertBodyExact(
            json_encode(['message' => 'Guests only'], JSON_THROW_ON_ERROR)
        );
        $psr_response->assertForbidden();
    }

    private function newMiddleware(
        ScopableWP $scopable_wp,
        string $redirect_url = null,
        string $json_message = null
    ): GuestsOnly {
        return new GuestsOnly($redirect_url, $json_message, $scopable_wp);
    }


}

class GuestScopableWP extends ScopableWP
{

    private bool $is_logged_in;

    public function __construct(bool $is_logged_in)
    {
        $this->is_logged_in = $is_logged_in;
    }

    public function isUserLoggedIn(): bool
    {
        return $this->is_logged_in;
    }

}