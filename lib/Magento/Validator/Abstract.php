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
 * Abstract validator class.
 */
abstract class Magento_Validator_Abstract implements Magento_Validator_Interface
{
    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * Clear messages
     */
    protected function _clearMessages()
    {
        $this->_messages = array();
    }

    /**
     * @param Magento_Validator_Interface $validator
     */
    protected function _addMessages($validator)
    {
        $this->_messages = array_merge_recursive($this->_messages, $validator->getMessages());
    }
}
