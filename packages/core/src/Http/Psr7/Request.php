<?php

declare(strict_types=1);

namespace Snicco\Core\Http\Psr7;

use WP_User;
use Snicco\Support\Str;
use Snicco\Core\Support\WP;
use Snicco\Core\Support\Url;
use Snicco\Core\Http\Cookies;
use Snicco\Core\Routing\Route;
use Snicco\Support\Repository;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\ServerRequestInterface;
use Snicco\Core\Traits\ValidatesWordpressNonces;

/**
 * @todo add ip() method
 */
class Request implements ServerRequestInterface
{
    
    use ImplementsPsr7Request;
    use InspectsRequest;
    use InteractsWithInput;
    use ValidatesWordpressNonces;
    
    public function __construct(ServerRequestInterface $psr_request)
    {
        $this->psr_request = $psr_request;
    }
    
    public function withRoute(Route $route) :Request
    {
        return $this->withAttribute('_route', $route);
    }
    
    public function withCookies(array $cookies) :Request
    {
        return $this->withAttribute('cookies', new Repository($cookies));
    }
    
    public function withUserId(int $user_id) :Request
    {
        return $this->withAttribute('_current_user_id', $user_id);
    }
    
    /**
     * @todo Figure out how psr7 immutability will affect this.
     */
    public function user() :WP_User
    {
        $user = $this->getAttribute('_current_user');
        
        if ( ! $user instanceof WP_User) {
            $this->psr_request =
                $this->psr_request->withAttribute('_current_user', $user = WP::currentUser());
            
            return $user;
        }
        
        return $user;
    }
    
    public function userId() :int
    {
        return $this->getAttribute('_current_user_id', 0);
    }
    
    public function authenticated() :bool
    {
        return WP::isUserLoggedIn();
    }
    
    public function userAgent()
    {
        return substr($this->getHeaderLine('User-Agent'), 0, 500);
    }
    
    // path + query + fragment
    public function fullRequestTarget() :string
    {
        $fragment = $this->getUri()->getFragment();
        
        return ($fragment !== '')
            ? $this->getRequestTarget().'#'.$fragment
            : $this->getRequestTarget();
    }
    
    public function url() :string
    {
        return preg_replace('/\?.*/', '', $this->getUri());
    }
    
    // host + path + query + fragment
    public function fullUrl() :string
    {
        return $this->getUri()->__toString();
    }
    
    /**
     * @internal
     */
    public function routingPath() :string
    {
        $uri = $this->getAttribute('routing.uri');
        
        /** @var UriInterface $uri */
        $uri = $uri ?? $this->getUri();
        
        return $uri->getPath();
    }
    
    public function loadingScript() :string
    {
        return trim($this->getServerParams()['SCRIPT_NAME'] ?? '', DIRECTORY_SEPARATOR);
    }
    
    public function cookies() :Repository
    {
        /** @var Repository $bag */
        $bag = $this->getAttribute('cookies', new Repository());
        
        if ($bag->all() === []) {
            $cookies = Cookies::parseHeader($this->getHeader('Cookie'));
            
            $bag->add($cookies);
        }
        
        return $bag;
    }
    
    public function route() :?Route
    {
        return $this->getAttribute('_route');
    }
    
    public function expires(int $default = 0) :int
    {
        return (int) $this->query('expires', $default);
    }
    
    public function isWpAdmin() :bool
    {
        // A request to the admin dashboard. We can catch that within admin_init
        return Str::contains($this->loadingScript(), 'wp-admin') && ! $this->isWpAjax();
    }
    
    public function isWpAjax() :bool
    {
        return Str::contains($this->loadingScript(), 'wp-admin/admin-ajax.php');
    }
    
    public function isWpFrontEnd() :bool
    {
        return ! ($this->isWpAjax() || $this->isWpAdmin())
               && Str::contains($this->loadingScript(), 'index.php');
    }
    
    public function path() :string
    {
        return $this->getUri()->getPath();
    }
    
    public function decodedPath() :string
    {
        return rawurldecode($this->path());
    }
    
    public function routeIs(...$patterns) :bool
    {
        $route = $this->route();
        
        if ( ! $route instanceof Route) {
            return false;
        }
        
        if (is_null($name = $route->getName())) {
            return false;
        }
        
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $name)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function fullUrlIs(...$patterns) :bool
    {
        $url = $this->fullUrl();
        
        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $url)) {
                return true;
            }
        }
        
        return false;
    }
    
    public function pathIs(...$patterns) :bool
    {
        $path = Url::addLeading($this->decodedPath());
        
        foreach ($patterns as $pattern) {
            if (Str::is(Url::addLeading($pattern), $path)) {
                return true;
            }
        }
        
        return false;
    }
    
}