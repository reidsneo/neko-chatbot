<?php

namespace Neko\Chatbot\Messages\Conversations;

use Neko\Chatbot\BotMan;
use Neko\Chatbot\Interfaces\ShouldQueue;
use Neko\Chatbot\Messages\Attachments\Audio;
use Neko\Chatbot\Messages\Attachments\Contact;
use Neko\Chatbot\Messages\Attachments\File;
use Neko\Chatbot\Messages\Attachments\Image;
use Neko\Chatbot\Messages\Attachments\Location;
use Neko\Chatbot\Messages\Attachments\Video;
use Neko\Chatbot\Messages\Incoming\IncomingMessage;
use Neko\Chatbot\Messages\Outgoing\Question;
use Closure;
use Illuminate\Support\Collection;
use Spatie\Macroable\Macroable;

/**
 * Class Conversation.
 */
abstract class Conversation
{
    use Macroable;

    /**
     * @var BotMan
     */
    protected $bot;

    /**
     * @var string
     */
    protected $token;

    /**
     * Number of minutes this specific conversation should be cached.
     * @var int
     */
    protected $cacheTime;

    /**
     * @param BotMan $bot
     */
    public function setBot(BotMan $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @return BotMan
     */
    public function getBot()
    {
        return $this->bot;
    }

    /**
     * @param string|Question $question
     * @param array|Closure $next
     * @param array $additionalParameters
     * @return $this
     */
    public function ask($question, $next, $additionalParameters = [])
    {
        $this->bot->reply($question, $additionalParameters);
        $this->bot->storeConversation($this, $next, $question, $additionalParameters);

        return $this;
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure $next
     * @param array|Closure $repeat
     * @param array $additionalParameters
     * @return $this
     */
    public function askForImages($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getImages';
        $additionalParameters['__pattern'] = Image::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure $next
     * @param array|Closure $repeat
     * @param array $additionalParameters
     * @return $this
     */
    public function askForFiles($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getFiles';
        $additionalParameters['__pattern'] = File::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure $next
     * @param array|Closure $repeat
     * @param array $additionalParameters
     * @return $this
     */
    public function askForVideos($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getVideos';
        $additionalParameters['__pattern'] = Video::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure $next
     * @param array|Closure $repeat
     * @param array $additionalParameters
     * @return $this
     */
    public function askForAudio($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getAudio';
        $additionalParameters['__pattern'] = Audio::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure $next
     * @param array|Closure $repeat
     * @param array $additionalParameters
     * @return $this
     */
    public function askForLocation($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getLocation';
        $additionalParameters['__pattern'] = Location::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $question
     * @param array|Closure                                    $next
     * @param array|Closure                                    $repeat
     * @param array                                            $additionalParameters
     *
     * @return $this
     */
    public function askForContact($question, $next, $repeat = null, $additionalParameters = [])
    {
        $additionalParameters['__getter'] = 'getContact';
        $additionalParameters['__pattern'] = Contact::PATTERN;
        $additionalParameters['__repeat'] = ! is_null($repeat) ? $this->bot->serializeClosure($repeat) : $repeat;

        return $this->ask($question, $next, $additionalParameters);
    }

    /**
     * Repeat the previously asked question.
     * @param string|Question $question
     */
    public function repeat($question = '')
    {
        $conversation = $this->bot->getStoredConversation();

        if (! $question instanceof Question && ! $question) {
            $question = unserialize($conversation['question']);
        }

        $next = $conversation['next'];
        $additionalParameters = unserialize($conversation['additionalParameters']);

        if (is_string($next)) {
            $next = unserialize($next)->getClosure();
        } elseif (is_array($next)) {
            $next = Collection::make($next)->map(function ($callback) {
                if ($this->bot->getDriver()->serializesCallbacks() && ! $this->bot->runsOnSocket()) {
                    $callback['callback'] = unserialize($callback['callback'])->getClosure();
                }

                return $callback;
            })->toArray();
        }
        $this->ask($question, $next, $additionalParameters);
    }

    /**
     * @param string|\Neko\Chatbot\Messages\Outgoing\Question $message
     * @param array $additionalParameters
     * @return $this
     */
    public function say($message, $additionalParameters = [])
    {
        $this->bot->reply($message, $additionalParameters);

        return $this;
    }

    /**
     * Should the conversation be skipped (temporarily).
     * @param  IncomingMessage $message
     * @return bool
     */
    public function skipsConversation(IncomingMessage $message)
    {
        //
    }

    /**
     * Should the conversation be removed and stopped (permanently).
     * @param  IncomingMessage $message
     * @return bool
     */
    public function stopsConversation(IncomingMessage $message)
    {
        //
    }

    /**
     * Override default conversation cache time (only for this conversation).
     * @return mixed
     */
    public function getConversationCacheTime()
    {
        return $this->cacheTime ?? null;
    }

    /**
     * @return mixed
     */
    abstract public function run();

    /**
     * @return array
     */
    public function __sleep()
    {
        $properties = get_object_vars($this);
        if (! $this instanceof ShouldQueue) {
            unset($properties['bot']);
        }

        return array_keys($properties);
    }
}
