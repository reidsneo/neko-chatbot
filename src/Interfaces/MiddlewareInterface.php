<?php

namespace Neko\Chatbot\Interfaces;

use Neko\Chatbot\Interfaces\Middleware\Captured;
use Neko\Chatbot\Interfaces\Middleware\Heard;
use Neko\Chatbot\Interfaces\Middleware\Matching;
use Neko\Chatbot\Interfaces\Middleware\Received;
use Neko\Chatbot\Interfaces\Middleware\Sending;

interface MiddlewareInterface extends Captured, Received, Matching, Heard, Sending
{
    //
}
