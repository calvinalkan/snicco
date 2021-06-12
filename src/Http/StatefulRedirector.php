<?php


    declare(strict_types = 1);


    namespace WPEmerge\Http;

    use Psr\Http\Message\ResponseFactoryInterface as Psr17ResponseFactory;
    use WPEmerge\Contracts\AbstractRedirector;
    use WPEmerge\Http\Psr7\Request;
    use WPEmerge\Http\Responses\RedirectResponse;
    use WPEmerge\Routing\UrlGenerator;
    use WPEmerge\Session\Session;

    class StatefulRedirector extends AbstractRedirector
    {

        /**
         * @var Session
         */
        private $session;

        public function __construct( Session $session, UrlGenerator $url_generator, Psr17ResponseFactory $response_factory)
        {
            $this->generator = $url_generator;
            $this->response_factory = $response_factory;
            $this->session = $session;

            parent::__construct($url_generator, $response_factory);

        }

        public function createRedirectResponse(string $path, int $status_code = 302) : RedirectResponse
        {

            $this->validateStatusCode($status_code);

            $psr_response = $this->response_factory->createResponse($status_code);

            return (new RedirectResponse($psr_response))
                ->to($path)
                ->withSession($this->session);


        }

        public function intended(Request $request, string $fallback = '', int $status = 302) : RedirectResponse
        {

            $path = $this->session->getIntendedUrl();

            if ($path) {
                return $this->createRedirectResponse($path, $status);
            }

            return parent::intended($request, $fallback, $status);
        }

        /**
         * Create a redirect response to the given path and store the intended url in the session.
         */
        public function guest (string $path, $status = 302, array $query = [],  bool $secure = true, bool $absolute = true ) {

            $request = $this->generator->getRequest();
            $session = $request->session();

            $intended = $request->getMethod() === 'GET' && ! $request->isAjax()
                ? $request->fullUrl()
                : $this->generator->previous('/', $session->getPreviousUrl('/'));

            if ($intended) {

                $request->session()->setIntendedUrl($intended);

            }

            return $this->to($path, $status, $query,  $secure, $absolute);

        }
    }