<?php


    declare(strict_types = 1);


    namespace Tests\integration\Auth\Controllers;

    use Tests\AuthTestCase;
    use Snicco\Contracts\EncryptorInterface;
    use Tests\integration\Auth\Stubs\TestTwoFactorProvider;
    use Snicco\Auth\Contracts\TwoFactorAuthenticationProvider;

    class TwoFactorAuthPreferenceControllerTest extends AuthTestCase
    {
	
	    private string $endpoint = '/auth/two-factor/preferences';
	
	    protected function setUp() :void
	    {
		
		    $this->afterLoadingConfig(function() {

                $this->with2Fa();
            });

            $this->afterApplicationCreated(function () {
                $this->withHeader('Accept', 'application/json');
                $this->encryptor = $this->app->resolve(EncryptorInterface::class);
            });

            parent::setUp();
        }

        /** @test */
        public function the_endpoint_is_not_accessible_with_2fa_disabled()
        {

            $this->without2Fa();
            $this->post($this->endpoint)->assertNullResponse();

        }

        /** @test */
        public function the_endpoint_is_not_accessible_if_not_authenticated()
        {

            $this->post($this->endpoint)
                 ->assertStatus(401);

        }

        /** @test */
        public function the_endpoint_is_not_accessible_if_auth_confirmation_is_expired()
        {

            $this->actingAs($calvin = $this->createAdmin());

            $this->travelIntoFuture(10);

            $this->post($this->endpoint)->assertRedirectToRoute('auth.confirm');


        }

        /** @test */
        public function an_error_is_returned_if_2fa_is_already_enabled_for_the_user()
        {
	
	        $this->actingAs($calvin = $this->createAdmin());
	        $this->generateTestSecret($calvin);
	
	        $response = $this->post($this->endpoint, [], ['Accept' => 'application/json']);
	
	        $response->assertStatus(409)
	                 ->assertIsJson()
	                 ->assertExactJson([
		                                   'message' => 'Two-Factor authentication is already enabled.',
	                                   ]);
	
        }

        /** @test */
        public function two_factor_authentication_can_be_enabled () {
	
	        $this->actingAs($calvin = $this->createAdmin());
	
	        $response = $this->post($this->endpoint);
	        $response->assertOk();
	
	        $body = $response->body();
	
	        $response = json_decode($body, true);
	        $this->assertCount(8, $response['recovery-codes']);
	        $codes_in_db = $this->getUserRecoveryCodes($calvin);
	        $this->assertSame($codes_in_db, $response['recovery-codes']);
	        $this->assertStringStartsWith('<svg', $response['qrcode']);
	
	        $this->assertIsString($this->getUserSecret($calvin));
	
        }
	
	    /** @test */
	    public function recovery_codes_are_encrypted()
	    {
		
		    $this->actingAs($calvin = $this->createAdmin());
		
		    $response = $this->post($this->endpoint);
		    $response->assertOk();
		
		    $body = $response->body();
		
		    $codes_in_body = json_decode($body, true);
		    $this->assertCount(8, $codes_in_body['recovery-codes']);
		
		    $codes_in_db = get_user_meta($calvin->ID, 'two_factor_recovery_codes', true);
		    $this->assertNotSame($codes_in_db, $codes_in_body);
		
		    $this->assertSame(
			    $codes_in_body['recovery-codes'],
			    json_decode($this->encryptor->decrypt($codes_in_db), true)
		    );
		
	    }
	
	    /** @test */
	    public function the_user_secret_is_stored_encrypted()
	    {
		
		    // Arrange
		    $this->actingAs($calvin = $this->createAdmin());
		    $this->swap(
			    TwoFactorAuthenticationProvider::class,
			    new TestTwoFactorProvider($this->encryptor)
		    );
		
		    // Act
		    $this->post($this->endpoint);
		
		    // Assert
		    $this->assertNotSame(
			    'secret',
			    $key = get_user_meta($calvin->ID, 'two_factor_secret', true),
			    'Two factor secret stored as plain text.'
		    );
		    $this->assertSame('secret', $this->encryptor->decryptString($key));
		
	    }
	
	    /** @test */
	    public function two_factor_authentication_can_not_be_disabled_for_user_who_dont_have_it_enabled()
	    {
		
		    $this->actingAs($calvin = $this->createAdmin());
		
		    $response = $this->delete($this->endpoint);
		    $response->assertStatus(409)->assertExactJson([
			                                                  'message' => 'Two-Factor authentication is not enabled.',
		                                                  ]);

        }

        /** @test */
        public function two_factor_authentication_can_be_disabled ()
        {
	
	        $this->actingAs($calvin = $this->createAdmin());
	        $this->generateTestSecret($calvin);
	        update_user_meta(
		        $calvin->ID,
		        'two_factor_recovery_codes',
		        $this->encryptCodes($this->generateTestRecoveryCodes())
	        );
	
	        $response = $this->delete($this->endpoint);
	        $response->assertNoContent();
	
	        $this->assertEmpty($this->getUserSecret($calvin));
	        $this->assertEmpty($this->getUserRecoveryCodes($calvin));
	
        }

    }
    
    