<?php

namespace Neko\Chatbot\Interfaces;

interface UserInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string|null
     */
    public function getUsername();

    /**
     * @return string|null
     */
    public function getFirstName();

    /**
     * @return string|null
     */
    public function getLastName();

    /**
     * Get raw driver's user info.
     * @return array
     */
    public function getInfo();
}
