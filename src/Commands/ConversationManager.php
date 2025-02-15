<?php

namespace Neko\Chatbot\Commands;

use Neko\Chatbot\Interfaces\DriverInterface;
use Neko\Chatbot\Messages\Attachments\Audio;
use Neko\Chatbot\Messages\Attachments\Contact;
use Neko\Chatbot\Messages\Attachments\File;
use Neko\Chatbot\Messages\Attachments\Image;
use Neko\Chatbot\Messages\Attachments\Location;
use Neko\Chatbot\Messages\Attachments\Video;
use Neko\Chatbot\Messages\Incoming\Answer;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;
use Neko\Chatbot\Messages\Matcher;
use Neko\Chatbot\Messages\Matching\MatchingMessage;
use Neko\Chatbot\Middleware\MiddlewareManager;
use Illuminate\Support\Collection;

class ConversationManager
{
    protected $matcher;

    public function __construct(?Matcher $matcher = null)
    {
        $this->matcher = $matcher ?? new Matcher();
    }

    /**
     * Messages to listen to.
     * @var Command[]
     */
    protected $listenTo = [];

    public function listenTo(Command $command)
    {
        $this->listenTo[] = $command;
    }

    /**
     * Add additional data (image,video,audio,location,files) data to
     * callable parameters.
     *
     * @param IncomingMessage $message
     * @param array $parameters
     * @return array
     */
    public function addDataParameters(IncomingMessage $message, array $parameters)
    {
        $messageText = $message->getText();

        if ($messageText === Image::PATTERN) {
            $parameters[] = $message->getImages();
        } elseif ($messageText === Video::PATTERN) {
            $parameters[] = $message->getVideos();
        } elseif ($messageText === Audio::PATTERN) {
            $parameters[] = $message->getAudio();
        } elseif ($messageText === Location::PATTERN) {
            $parameters[] = $message->getLocation();
        } elseif ($messageText === Contact::PATTERN) {
            $parameters[] = $message->getContact();
        } elseif ($messageText === File::PATTERN) {
            $parameters[] = $message->getFiles();
        }

        return $parameters;
    }

    /**
     * @param IncomingMessage[] $messages
     * @param MiddlewareManager $middleware
     * @param Answer $answer
     * @param DriverInterface $driver
     * @param bool $withReceivedMiddleware
     * @return array|MatchingMessage[]
     */
    public function getMatchingMessages($messages, MiddlewareManager $middleware, Answer $answer, DriverInterface $driver, $withReceivedMiddleware = true): array
    {
        $messages = Collection::make($messages)->reject(function (IncomingMessage $message) {
            return $message->isFromBot();
        });

        $matchingMessages = [];
        foreach ($messages as $message) {
            if ($withReceivedMiddleware) {
                $message = $middleware->applyMiddleware('received', $message);
            }

            foreach ($this->listenTo as $command) {
                if ($this->matcher->isMessageMatching($message, $answer, $command, $driver, $middleware->matching())) {
                    $matchingMessages[] = new MatchingMessage($command, $message, $this->matcher->getMatches());
                }
            }
        }

        return $matchingMessages;
    }
}
