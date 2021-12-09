<?php

declare(strict_types=1);

namespace Snicco\Core\EventDispatcher\Events;


use Snicco\Core\Support\Arr;

class IncomingAjaxRequest extends IncomingRequest
{
    
    public function shouldDispatch() :bool
    {
        if ( ! $this->request->isWpAjax()) {
            return false;
        }
        
        if ($this->request->isReadVerb()) {
            return Arr::has($this->request->getQueryParams(), 'action');
        }
        
        return Arr::has($this->request->getParsedBody(), 'action');
    }
    
}