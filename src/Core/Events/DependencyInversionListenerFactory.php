<?php

declare(strict_types=1);

namespace Snicco\Core\Events;

use Closure;
use Throwable;
use Contracts\ContainerAdapter;
use Snicco\EventDispatcher\Listener;
use Snicco\EventDispatcher\Contracts\Event;
use Snicco\EventDispatcher\Contracts\ListenerFactory;
use Snicco\EventDispatcher\Exceptions\ListenerCreationException;

final class DependencyInversionListenerFactory implements ListenerFactory
{
    
    private ContainerAdapter $container_adapter;
    
    /** @todo replace after betterwphooks removal */
    public function __construct(ContainerAdapter $container_adapter)
    {
        $this->container_adapter = $container_adapter;
    }
    
    public function create($listener, Event $event) :Listener
    {
        if ($listener instanceof Closure) {
            return new Listener($listener);
        }
        try {
            $instance = $this->container_adapter->make($listener[0]);
            $this->container_adapter->instance(get_class($instance), $instance);
        } catch (Throwable $e) {
            throw ListenerCreationException::becauseTheListenerWasNotInstantiatable(
                $listener,
                $event->getName(),
                $e
            );
        }
        
        return new Listener(fn(...$payload) => $instance->{$listener[1]}(...$payload));
    }
    
}