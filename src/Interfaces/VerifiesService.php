<?php

namespace Neko\Chatbot\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface VerifiesService
{
    public function verifyRequest(Request $request);
}
