<?php

declare(strict_types=1);

namespace Snicco\Auth\Contracts;

use Snicco\Core\Http\Psr7\Request;
use Snicco\Core\Contracts\ResponseFactory;

interface AuthConfirmation
{
    
    public function confirm(Request $request) :bool;
    
    /**
     * Return anything that can be converted to a response object.
     *
     * @see ResponseFactory::toResponse()
     */
    public function view(Request $request);
    
}