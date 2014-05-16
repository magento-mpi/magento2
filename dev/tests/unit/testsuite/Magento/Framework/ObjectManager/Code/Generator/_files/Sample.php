<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\ObjectManager\Code\Generator;

/**
 * Class Sample for Proxy and Factory generation
 * @package Magento\Framework\ObjectManager\Code\Generator
 */
class Sample
{
    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}