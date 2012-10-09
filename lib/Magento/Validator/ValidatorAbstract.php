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
abstract class Magento_Validator_ValidatorAbstract implements Magento_Validator_ValidatorInterface
{
    /**
     * Array of validation failure messages
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Get validation failure messages
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
     * Add messages
     *
     * @param array $messages
     */
    protected function _addMessages(array $messages)
    {
        $this->_messages = array_merge_recursive($this->_messages, $messages);
    }
}
