<?php

namespace Neko\Chatbot\Messages\Outgoing;

use Neko\Chatbot\Messages\Attachments\Attachment;

class OutgoingMessage
{
    /** @var string */
    protected $message;

    /** @var \Neko\Chatbot\Messages\Attachments\Attachment */
    protected $attachment;

    /**
     * IncomingMessage constructor.
     * @param string $message
     * @param Attachment $attachment
     */
    public function __construct($message = null, Attachment $attachment = null)
    {
        $this->message = $message;
        $this->attachment = $attachment;
    }

    /**
     * @param string $message
     * @param Attachment $attachment
     * @return OutgoingMessage
     */
    public static function create($message = null, Attachment $attachment = null)
    {
        return new static($message, $attachment);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function text($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @param \Neko\Chatbot\Messages\Attachments\Attachment $attachment
     * @return $this
     */
    public function withAttachment(Attachment $attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * @return \Neko\Chatbot\Messages\Attachments\Attachment
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->message;
    }
}
