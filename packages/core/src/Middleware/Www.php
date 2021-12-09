<?php

declare(strict_types=1);

namespace Snicco\Core\Middleware;

use Snicco\Support\Str;
use Snicco\Core\Http\Delegate;
use Snicco\Core\Http\Psr7\Request;
use Snicco\Core\Contracts\Middleware;
use Psr\Http\Message\ResponseInterface;

class Www extends Middleware
{
    
    protected $with_www;
    
    public function __construct(string $site_url)
    {
        $this->with_www = strpos($site_url, 'www.');
    }
    
    public function handle(Request $request, Delegate $next) :ResponseInterface
    {
        if ( ! $request->isWpFrontEnd()) {
            return $next($request);
        }
        
        $uri = $request->getUri();
        $host = $uri->getHost();
        
        $contains_www = strpos($host, 'www.') === 0;
        
        if ($contains_www && $this->with_www === false) {
            $host = Str::after($host, 'www.');
            $uri = $uri->withHost($host);
            
            return $this->response_factory->permanentRedirectTo((string) $uri);
        }
        
        if ( ! $contains_www && $this->with_www && $this->wwwCanBeAdded($host)) {
            $host = 'www.'.$host;
            $uri = $uri->withHost($host);
            
            return $this->response_factory->permanentRedirectTo((string) $uri);
        }
        
        return $next($request);
    }
    
    private function wwwCanBeAdded(string $host) :bool
    {
        //is an ip?
        if (empty($host) || filter_var($host, FILTER_VALIDATE_IP)) {
            return false;
        }
        
        //is "localhost" or similar?
        $pieces = explode('.', $host);
        
        return count($pieces) > 1 && $pieces[0] !== 'www';
    }
    
}