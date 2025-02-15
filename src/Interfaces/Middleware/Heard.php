<?php

namespace Neko\Chatbot\Interfaces\Middleware;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;

interface Heard
{
    /**
     * Handle a message that was successfully heard, but not processed yet.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function heard(IncomingMessage $message, $next, BotMan $bot);
}
