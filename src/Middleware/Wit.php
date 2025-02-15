<?php

namespace Neko\Chatbot\Middleware;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Http\Curl;
use Neko\Chatbot\Interfaces\HttpInterface;
use Neko\Chatbot\Interfaces\MiddlewareInterface;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;
use Illuminate\Support\Collection;

class Wit implements MiddlewareInterface
{
    /** @var string */
    protected $token;

    /** @var float */
    protected $minimumConfidence = 0.5;

    /** @var HttpInterface */
    protected $http;

    /** @var \stdClass */
    protected $response;

    /**
     * Wit constructor.
     * @param string $token wit.ai access token
     * @param float $minimumConfidence Minimum confidence value to match against
     * @param HttpInterface $http
     */
    public function __construct($token, $minimumConfidence, HttpInterface $http)
    {
        $this->token = $token;
        $this->minimumConfidence = $minimumConfidence;
        $this->http = $http;
    }

    /**
     * Create a new Wit middleware instance.
     * @param string $token wit.ai access token
     * @param float $minimumConfidence
     * @return Wit
     */
    public static function create($token, $minimumConfidence = 0.5)
    {
        return new static($token, $minimumConfidence, new Curl());
    }

    protected function getResponse(IncomingMessage $message)
    {
        $endpoint = 'https://api.wit.ai/message?q='.urlencode($message->getText());

        $this->response = $this->http->get($endpoint, [], [
            'Authorization: Bearer '.$this->token,
        ]);

        return $this->response;
    }

    /**
     * Handle a captured message.
     *
     * @param \Neko\Chatbot\Messages\Incoming\IncomingMessage $message
     * @param BotMan $bot
     * @param $next
     *
     * @return mixed
     */
    public function captured(IncomingMessage $message, $next, BotMan $bot)
    {
        return $next($message);
    }

    /**
     * Handle an incoming message.
     *
     * @param \Neko\Chatbot\Messages\Incoming\IncomingMessage $message
     * @param BotMan $bot
     * @param $next
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $response = $this->getResponse($message);

        $responseData = Collection::make(json_decode($response->getContent(), true));
        $message->addExtras('entities', $responseData->get('entities'));
        $message->addExtras('intents', $responseData->get('intents'));

        return $next($message);
    }

    /**
     * @param \Neko\Chatbot\Messages\Incoming\IncomingMessage $message
     * @param string $pattern
     * @param bool $regexMatched Indicator if the regular expression was matched too
     * @return bool
     */
    public function matching(IncomingMessage $message, $pattern, $regexMatched)
    {
        $intents = Collection::make($message->getExtras())->get('intents', []);

        if (!empty($intents)) {
            foreach ($intents as $intent) {
                if (($intent['name'] === $pattern || $intent['id'] === $pattern) && $intent['confidence'] >= $this->minimumConfidence) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handle a message that was successfully heard, but not processed yet.
     *
     * @param \Neko\Chatbot\Messages\Incoming\IncomingMessage $message
     * @param BotMan $bot
     * @param $next
     *
     * @return mixed
     */
    public function heard(IncomingMessage $message, $next, BotMan $bot)
    {
        return $next($message);
    }

    /**
     * Handle an outgoing message payload before/after it
     * hits the message service.
     *
     * @param mixed $payload
     * @param BotMan $bot
     * @param $next
     *
     * @return mixed
     */
    public function sending($payload, $next, BotMan $bot)
    {
        return $next($payload);
    }
}
