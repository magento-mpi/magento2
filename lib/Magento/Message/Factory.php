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
        InterfaceMessage::TYPE_ERROR,
        InterfaceMessage::TYPE_WARNING,
        InterfaceMessage::TYPE_NOTICE,
        InterfaceMessage::TYPE_SUCCESS,
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
     * @return InterfaceMessage
     */
    public function create($type, $text = '')
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Wrong message type');
        }

        $className = 'Magento\Message\\' . ucfirst($type);
        $message = $this->objectManager->create($className, array('text' => $text));
        if (!($message instanceof InterfaceMessage)) {
            throw new \InvalidArgumentException($className . ' doesn\'t implement \Magento\Message\InterfaceMessage');
        }

        return $message;
    }
}
