<?php

namespace Neko\Chatbot\Interfaces;

use Neko\Chatbot\BotMan;

interface ExceptionHandlerInterface
{
    /**
     * Handle an exception.
     *
     * @param \Throwable $e
     * @param BotMan $bot
     * @return mixed
     */
    public function handleException($e, BotMan $bot);

    /**
     * Register a new exception type.
     *
     * @param string $exception
     * @param callable $closure
     * @return mixed
     */
    public function register(string $exception, callable $closure);
}
