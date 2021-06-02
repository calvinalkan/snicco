<?php


    declare(strict_types = 1);


    namespace WPEmerge\Http;

    use Psr\Http\Message\ResponseFactoryInterface;
    use Psr\Http\Message\ResponseFactoryInterface as Psr17ResponseFactory;
    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\StreamFactoryInterface as Psr17StreamFactory;
    use Psr\Http\Message\StreamInterface;
    use WPEmerge\Contracts\ResponsableInterface;
    use WPEmerge\Contracts\ViewFactoryInterface as ViewFactory;
    use WPEmerge\ExceptionHandling\Exceptions\HttpException;
    use WPEmerge\ExceptionHandling\Exceptions\ViewException;
    use WPEmerge\Facade\WP;
    use WPEmerge\Http\Psr7\Response;
    use WPEmerge\Http\Responses\InvalidResponse;
    use WPEmerge\Http\Responses\NullResponse;
    use WPEmerge\Http\Responses\RedirectResponse;
    use WPEmerge\Http\Responses\WpQueryFilteredResponse;

    class ResponseFactory implements ResponseFactoryInterface
    {

        /**
         * @var ViewFactory
         */
        private $view;
        /**
         * @var Psr17ResponseFactory
         */
        private $response_factory;

        /**
         * @var Psr17StreamFactory
         */
        private $stream_factory;

        public function __construct(ViewFactory $view, Psr17ResponseFactory $response, Psr17StreamFactory $stream)
        {

            $this->view = $view;
            $this->response_factory = $response;
            $this->stream_factory = $stream;

        }

        public function view(string $view, array $data = [], $status = 200, array $headers = []) : Response
        {

            $content = $this->view->make($view)->with($data)->toString();

            $psr_response = $this->make($status)
                                 ->html($this->stream_factory->createStream($content));

            $response = new Response($psr_response);

            foreach ($headers as $name => $value) {

                $response = $response->withHeader($name, $value);

            }

            return $response;

        }

        public function make(int $status_code = 200, $reason_phrase = '') : Response
        {

            $psr_response = $this->response_factory->createResponse($status_code, $reason_phrase);

            return new Response($psr_response);

        }

        public function html(string $html, int $status_code = 200) : Response
        {

            return $this->make($status_code)
                        ->html($this->stream_factory->createStream($html));

        }

        public function json($content, int $status = 200) : Response
        {

            /** @todo This needs more parsing or a dedicated JsonResponseClass */
            return $this->make($status)
                        ->json(
                            $this->createStream(json_encode($content))
                        );

        }

        public function null() : NullResponse
        {

            return new NullResponse($this->response_factory->createResponse(204));

        }

        public function queryFiltered () : WpQueryFilteredResponse {

            return new WpQueryFilteredResponse($this->response_factory->createResponse(200));

        }

        public function toResponse($response) : Response
        {

            if ($response instanceof Response) {

                return $response;

            }

            if ($response instanceof ResponseInterface) {

                return new Response($response);

            }

            if (is_string($response)) {

                return $this->html($response);

            }

            if (is_array($response)) {

                return $this->json($response);

            }

            if ($response instanceof ResponsableInterface) {

                return $this->toResponse(
                    $response->toResponsable()
                );

            }

            return $this->invalidResponse();

        }

        public function redirect(int $status_code = 302) : RedirectResponse
        {
            return new RedirectResponse($this->make($status_code));
        }

        public function invalidResponse() : InvalidResponse
        {

            return new InvalidResponse($this->response_factory->createResponse(500));
        }

        public function createResponse(int $code = 200, string $reasonPhrase = '') : ResponseInterface
        {

            return $this->response_factory->createResponse($code, $reasonPhrase);
        }

        public function createStream(string $content = '') : StreamInterface
        {

            return $this->stream_factory->createStream($content);
        }

        public function createStreamFromFile(string $filename, string $mode = 'r') : StreamInterface
        {

            return $this->stream_factory->createStreamFromFile($filename, $mode);
        }

        public function createStreamFromResource($resource) : StreamInterface
        {

            return $this->stream_factory->createStreamFromResource($resource);
        }

        public function error(HttpException $e) : Response
        {

            if ( $e->isAjax() ) {

                return $this->json(
                    $e->getMessageForHumans(),
                    (int) $e->getStatusCode()
                );

            }

            $views = ['error', 'index'];

            if (WP::isAdmin()) {

                $views = collect($views)->map(function ($view) {

                    return $view.'-'.(WP::isAdminAjax() ? 'ajax' : 'admin');

                })->merge($views)->all();

            }

            $view = $this->view->make($views)->with([
                    'code'=> $e->getStatusCode(),
                    'message' => $e->getMessageForHumans(),
                ]
            );

            try {

                return $this->toResponse($view)->withStatus( (int) $e->getStatusCode() );

            }
            catch (ViewException $e) {

                return $this->toResponse($this->view->make('500'))->withStatus(500);

            }


        }

        public function noContent() : ResponseInterface
        {
            return $this->createResponse();
        }

    }