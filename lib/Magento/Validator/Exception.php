<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Exception class for validator
 */
class Magento_Validator_Exception extends Exception
{
    /**
     * @var array
     */
    protected $_messages;

    public function __construct(array $messages)
    {
        $this->_messages = $messages;

        $message = '';
        foreach ($this->_messages as $propertyMessages) {
            foreach ($propertyMessages as $propertyMessage) {
                if ($message) {
                    $message .= PHP_EOL;
                }
                $message .= $propertyMessage;
            }
        }

        parent::__construct($message);
    }

    public function getMessages()
    {
        return $this->_messages;
    }
}
