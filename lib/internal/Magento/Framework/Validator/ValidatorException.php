<?php
/**
 * \Exception class for validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Validator;

class ValidatorException extends \Magento\Framework\Exception\InputException
{
    /**
     * @var array
     */
    protected $_messages;

    /**
     * Constructor
     *
     * @param string $message
     * @param [] $params
     * @param \Exception $cause
     * @param array $messages Validation error messages
     */
    public function __construct(
        $message = self::DEFAULT_MESSAGE,
        $params = [],
        \Exception $cause = null,
        array $messages = array()
    ) {
        if (!empty($messages)) {
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
        }
        parent::__construct($message, $params, $cause);
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
