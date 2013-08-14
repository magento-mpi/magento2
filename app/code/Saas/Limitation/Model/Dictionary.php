<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Dictionary
{
    /**
     * @var Magento_Core_Helper_Abstract
     */
    private $_translationHelper;

    /**
     * @var array
     */
    private $_messages;

    /**
     * @param Magento_Core_Helper_Abstract $translationHelper
     * @param array $messages Format: array(<message_code> => <message_text>, ...)
     */
    public function __construct(Magento_Core_Helper_Abstract $translationHelper, array $messages)
    {
        $this->_translationHelper = $translationHelper;
        $this->_messages = $messages;
    }

    /**
     * Retrieve translated message by its code
     *
     * @param string $code
     * @return string
     * @throws InvalidArgumentException
     */
    public function getMessage($code)
    {
        if (!isset($this->_messages[$code])) {
            throw new InvalidArgumentException("Message '$code' has not been defined.");
        }
        return $this->_translationHelper->__($this->_messages[$code]);
    }
}
