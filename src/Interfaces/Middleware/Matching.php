<?php

namespace Neko\Chatbot\Interfaces\Middleware;

use Neko\Chatbot\Messages\Incoming\IncomingMessage;

interface Matching
{
    /**
     * @param IncomingMessage $message
     * @param string $pattern
     * @param bool $regexMatched Indicator if the regular expression was matched too
     * @return bool
     */
    public function matching(IncomingMessage $message, $pattern, $regexMatched);
}
