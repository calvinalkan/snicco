<?php

declare(strict_types=1);

namespace Snicco\Core\Routing;

use Snicco\Core\Support\WP;
use Snicco\Support\Str;
use Snicco\Core\Contracts\ConvertsToUrl;

class AdminRoute extends Route implements ConvertsToUrl
{
    
    public function toUrl(array $arguments = []) :string
    {
        $url = $this->getUrl();
        
        $parts = explode('/', Str::after(ltrim($url, '/'), '/'));
        
        return WP::adminUrl("$parts[0]?page=$parts[1]");
    }
    
}