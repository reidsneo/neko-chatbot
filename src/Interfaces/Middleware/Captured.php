<?php

namespace Neko\Chatbot\Interfaces\Middleware;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;

interface Captured
{
    /**
     * Handle a captured message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function captured(IncomingMessage $message, $next, BotMan $bot);
}
