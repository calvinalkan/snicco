<?php


    declare(strict_types = 1);


    namespace WPEmerge\Http;

    use Nyholm\Psr7\Factory\Psr17Factory as NyholmFactoryImplementation;
    use Psr\Http\Message\ResponseFactoryInterface as Prs17ResponseFactory;
    use Psr\Http\Message\StreamFactoryInterface;
    use WPEmerge\Contracts\ServiceProvider;
    use WPEmerge\Contracts\ViewFactoryInterface;
    use WPEmerge\Routing\Pipeline;

    class HttpServiceProvider extends ServiceProvider
    {

        public function register() : void
        {

            $this->bindKernel();

            $this->bindConcretePsr17ResponseFactory();

            $this->bindPsr17ResponseFactoryInterface();

            $this->bindPsr17StreamFactory();

            $this->bindCookies();

        }

        public function bootstrap() : void
        {

            /** @var HttpKernel $kernel */
            $kernel = $this->container->make(HttpKernel::class);

            if ($this->config->get('middleware.always_run_global', false)) {

                $kernel->alwaysWithGlobalMiddleware($this->config->get('middleware.groups.global', [] ) );

            }



        }

        private function bindKernel()
        {

            $this->container->singleton(HttpKernel::class, function () {

                return new HttpKernel(

                    $this->container->make(Pipeline::class),

                );

            });
        }

        private function bindConcretePsr17ResponseFactory() : void
        {

            $this->container->singleton('psr17.response.factory', function () {

                return new NyholmFactoryImplementation();

            });
        }

        private function bindPsr17StreamFactory() : void
        {

            $this->container->singleton(StreamFactoryInterface::class, function () {

                return new NyholmFactoryImplementation();

            });
        }

        private function bindPsr17ResponseFactoryInterface() : void
        {

            $this->container->singleton(ResponseFactory::class, function () {

                return new ResponseFactory(
                    $this->container->make(ViewFactoryInterface::class),
                    $this->container->make('psr17.response.factory'),
                    $this->container->make(StreamFactoryInterface::class),

                );

            });

            $this->container->singleton(Prs17ResponseFactory::class, function () {

                return $this->container->make(ResponseFactory::class);

            });

        }

        private function bindCookies()
        {

            $this->container->singleton(Cookies::class, function () {

                $cookies = new Cookies();
                $cookies->setDefaults([
                    'value' => '',
                    'domain' => null,
                    'hostonly' => true,
                    'path' => null,
                    'expires' => null,
                    'secure' => true,
                    'httponly' => false,
                    'samesite' => 'lax'
                ]);

                return $cookies;

            });
        }


    }