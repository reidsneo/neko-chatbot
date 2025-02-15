<?php

namespace Neko\Chatbot\Messages\Matching;

use Neko\Chatbot\Commands\Command;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;

class MatchingMessage
{
    /** @var Command */
    protected $command;

    /** @var IncomingMessage */
    protected $message;

    /** @var array */
    private $matches;

    /**
     * MatchingMessage constructor.
     * @param Command $command
     * @param IncomingMessage $message
     * @param array $matches
     */
    public function __construct(Command $command, IncomingMessage $message, array $matches)
    {
        $this->command = $command;
        $this->message = $message;
        $this->matches = $matches;
    }

    /**
     * @return Command
     */
    public function getCommand(): Command
    {
        return $this->command;
    }

    /**
     * @return \Neko\Chatbot\Messages\Incoming\IncomingMessage
     */
    public function getMessage(): IncomingMessage
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getMatches(): array
    {
        return $this->matches;
    }
}
