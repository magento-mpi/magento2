<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Message model factory
 */
class Factory
{
    /**
     * Allowed message types
     *
     * @var array
     */
    protected $types = array(
        MessageInterface::TYPE_ERROR,
        MessageInterface::TYPE_WARNING,
        MessageInterface::TYPE_NOTICE,
        MessageInterface::TYPE_SUCCESS,
    );

    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create message instance with specified parameters
     *
     * @param string $type
     * @param string $text
     * @throws \InvalidArgumentException
     * @return MessageInterface
     */
    public function create($type, $text)
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Wrong message type');
        }

        $className = 'Magento\Message\\' . ucfirst($type);
        $message = $this->objectManager->create($className, array('text' => $text));
        if (!($message instanceof MessageInterface)) {
            throw new \InvalidArgumentException($className . ' doesn\'t implement \Magento\Message\MessageInterface');
        }

        return $message;
    }
}
