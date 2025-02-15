<?php

namespace Neko\Chatbot\Interfaces\Middleware;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;

interface Received
{
    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot);
}
