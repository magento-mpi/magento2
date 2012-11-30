<?php
/**
 * Exception class for validator
 *
 * @copyright {}
 */
class Magento_Validator_Exception extends Exception
{
    /**
     * @var array
     */
    protected $_messages;

    /**
     * Constructor
     *
     * @param array $messages Validation error messages
     */
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

    /**
     * Get validation error messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
