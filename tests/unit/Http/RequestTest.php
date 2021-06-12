<?php


    declare(strict_types = 1);


    namespace Tests\unit\Http;

    use Tests\stubs\TestRequest;
    use Tests\UnitTest;
    use WPEmerge\Http\Psr7\Request;
    use WPEmerge\Routing\RoutingResult;
    use WPEmerge\Session\Drivers\ArraySessionDriver;
    use WPEmerge\Session\Session;
    use WPEmerge\Support\VariableBag;
    use WPEmerge\Validation\Validator;

    class RequestTest extends UnitTest
    {


        /**
         * @var Request
         */
        private $request;

        protected function setUp() : void
        {

            parent::setUp();

            $this->request = TestRequest::from('GET', 'foo');
        }

        public function testIsImmutable()
        {

            $request = TestRequest::from('GET', 'foo');

            $next = $request->withMethod('POST');

            $this->assertInstanceOf(Request::class, $request);
            $this->assertInstanceOf(Request::class, $next);

            $this->assertNotSame($request, $next);

            $this->assertSame('GET', $request->getMethod());
            $this->assertSame('POST', $next->getMethod());


        }

        public function testGetPath()
        {

            $request = TestRequest::from('GET', '/foo/bar');
            $this->assertSame('/foo/bar', $request->path());

            $request = TestRequest::from('GET', '/foo/bar/');
            $this->assertSame('/foo/bar/', $request->path());

            $request = TestRequest::from('GET', '/');
            $this->assertSame('/', $request->path());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz');
            $this->assertSame('/foo/bar', $request->path());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/?baz=biz');
            $this->assertSame('/foo/bar/', $request->path());

        }

        public function testGetFullPath() {

            $request = TestRequest::from('GET', '/foo/bar');
            $this->assertSame('/foo/bar', $request->fullPath());

            $request = TestRequest::from('GET', '/foo/bar/');
            $this->assertSame('/foo/bar/', $request->fullPath());

            $request = TestRequest::from('GET', '/');
            $this->assertSame('/', $request->fullPath());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz');
            $this->assertSame('/foo/bar?baz=biz', $request->fullPath());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/?baz=biz');
            $this->assertSame('/foo/bar/?baz=biz', $request->fullPath());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz#section');
            $this->assertSame('/foo/bar?baz=biz#section', $request->fullPath());



        }

        public function testGetUrl () {


            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar');
            $this->assertSame('https://foo.com/foo/bar', $request->url());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/');
            $this->assertSame('https://foo.com/foo/bar/', $request->url());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz');
            $this->assertSame('https://foo.com/foo/bar', $request->url());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/?baz=biz');
            $this->assertSame('https://foo.com/foo/bar/', $request->url());


        }

        public function testGetFullUrl () {

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar');
            $this->assertSame('https://foo.com/foo/bar', $request->fullUrl());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/');
            $this->assertSame('https://foo.com/foo/bar/', $request->fullUrl());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz');
            $this->assertSame('https://foo.com/foo/bar?baz=biz', $request->fullUrl());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar/?baz=biz');
            $this->assertSame('https://foo.com/foo/bar/?baz=biz', $request->fullUrl());

            $request = TestRequest::fromFullUrl('GET', 'https://foo.com/foo/bar?baz=biz#section');
            $this->assertSame('https://foo.com/foo/bar?baz=biz#section', $request->fullUrl());

        }

        public function testCookies()
        {

            $cookies = $this->request->cookies();
            $this->assertInstanceOf(VariableBag::class, $cookies);
            $this->assertSame([], $cookies->all());

            $request = $this->request->withCookies(['foo' => 'bar']);
            $cookies = $request->cookies();
            $this->assertInstanceOf(VariableBag::class, $cookies);
            $this->assertSame(['foo' => 'bar'], $cookies->all());

        }

        public function testSession()
        {

            try {

                $this->request->session();

                $this->fail('Missing session did not throw an exception');

            }
            catch (\RuntimeException $e) {

                $this->assertSame('A session has not been set on the request.', $e->getMessage());

            }

            $request = $this->request->withSession($session = new Session('cookie', new ArraySessionDriver(10)));

            $request = $request->withMethod('POST');

            $this->assertSame($session, $request->session());

        }

        public function testIsRouteable()
        {

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'index.php']);
            $this->assertTrue($request->isRouteable());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/wp-login.php']);
            $request = $request->withAttribute('_wp_admin_folder', 'wp-admin');
            $this->assertTrue($request->isRouteable());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/admin-ajax.php']);
            $request = $request->withAttribute('_wp_admin_folder', 'wp-admin');
            $this->assertTrue($request->isRouteable());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-login.php']);
            $this->assertFalse($request->isRouteable());


        }

        public function testGetLoadingScript()
        {

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'index.php']);
            $this->assertSame('index.php', $request->loadingScript());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/edit.php']);
            $this->assertSame('wp-admin/edit.php', $request->loadingScript());


        }

        public function testIsWpAdmin()
        {

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'index.php']);
            $this->assertFalse($request->isWpAdmin());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/edit.php']);
            $this->assertTrue($request->isWpAdmin());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/admin-ajax.php']);
            $this->assertFalse($request->isWpAdmin());


        }

        public function testIsWpAjax()
        {

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'index.php']);
            $this->assertFalse($request->isWpAjax());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/edit.php']);
            $this->assertFalse($request->isWpAjax());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/admin-ajax.php']);
            $this->assertTrue($request->isWpAjax());


        }

        public function testisWpFrontEnd()
        {

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'index.php']);
            $this->assertTrue($request->isWpFrontend());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/edit.php']);
            $this->assertFalse($request->isWpFrontend());

            $request = TestRequest::withServerParams($this->request, ['SCRIPT_NAME' => 'wp-admin/admin-ajax.php']);
            $this->assertFalse($request->isWpFrontend());

        }

        public function testGetRoutingResult()
        {

            $result = $this->request->routingResult();
            $this->assertInstanceOf(RoutingResult::class, $result);
            $this->assertNull($result->route());
            $this->assertSame([], $result->capturedUrlSegmentValues());

            $request = $this->request->withRoutingResult(new RoutingResult(['route'], ['foo' => 'bar']));
            $result = $request->routingResult();
            $this->assertInstanceOf(RoutingResult::class, $result);
            $this->assertNotNull($result);
            $this->assertSame(['foo' => 'bar'], $result->capturedUrlSegmentValues());

        }

        public function testValidator()
        {

            try {

                $this->request->validator();

                $this->fail('Missing validator did not throw an exception');

            }
            catch (\RuntimeException $e) {

                $this->assertSame('A validator instance has not been set on the request.', $e->getMessage());

            }

            $request = $this->request->withValidator($v = new Validator());

            $request = $request->withMethod('POST');

            $this->assertSame($v, $request->validator());

        }

    }