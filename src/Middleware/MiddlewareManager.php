<?php

namespace Neko\Chatbot\Middleware;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Interfaces\Middleware\Captured;
use Neko\Chatbot\Interfaces\Middleware\Heard;
use Neko\Chatbot\Interfaces\Middleware\Matching;
use Neko\Chatbot\Interfaces\Middleware\Received;
use Neko\Chatbot\Interfaces\Middleware\Sending;
use Neko\Chatbot\Interfaces\MiddlewareInterface;
use Closure;
use Mpociot\Pipeline\Pipeline;

class MiddlewareManager
{
    /** @var Received[] */
    protected $received = [];
    /** @var Captured[] */
    protected $captured = [];
    /** @var Matching[] */
    protected $matching = [];
    /** @var Heard[] */
    protected $heard = [];
    /** @var Sending[] */
    protected $sending = [];
    /** @var BotMan */
    protected $botman;

    public function __construct(BotMan $botman)
    {
        $this->botman = $botman;
    }

    /**
     * @param Received[] ...$middleware
     * @return Received[]|$this
     */
    public function received(Received ...$middleware)
    {
        if (empty($middleware)) {
            return $this->received;
        }
        $this->received = array_merge($this->received, $middleware);

        return $this;
    }

    /**
     * @param Captured[] ...$middleware
     * @return Captured[]|$this
     */
    public function captured(Captured ...$middleware)
    {
        if (empty($middleware)) {
            return $this->captured;
        }
        $this->captured = array_merge($this->captured, $middleware);

        return $this;
    }

    /**
     * @param Matching[] ...$middleware
     * @return Matching[]|$this
     */
    public function matching(Matching ...$middleware)
    {
        if (empty($middleware)) {
            return $this->matching;
        }
        $this->matching = array_merge($this->matching, $middleware);

        return $this;
    }

    /**
     * @param Heard[] $middleware
     * @return Heard[]|$this
     */
    public function heard(Heard ...$middleware)
    {
        if (empty($middleware)) {
            return $this->heard;
        }
        $this->heard = array_merge($this->heard, $middleware);

        return $this;
    }

    /**
     * @param Sending[] $middleware
     * @return Sending[]|$this
     */
    public function sending(Sending ...$middleware)
    {
        if (empty($middleware)) {
            return $this->sending;
        }
        $this->sending = array_merge($this->sending, $middleware);

        return $this;
    }

    /**
     * @param string $method
     * @param mixed $payload
     * @param MiddlewareInterface[] $additionalMiddleware
     * @param Closure|null $destination
     * @return mixed
     */
    public function applyMiddleware($method, $payload, array $additionalMiddleware = [], Closure $destination = null)
    {
        $destination = is_null($destination) ? function ($payload) {
            return $payload;
        }
        : $destination;

        $middleware = $this->$method + $additionalMiddleware;

        return (new Pipeline())
            ->via($method)
            ->send($payload)
            ->with($this->botman)
            ->through($middleware)
            ->then($destination);
    }
}
