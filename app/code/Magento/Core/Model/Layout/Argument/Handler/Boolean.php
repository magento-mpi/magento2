<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument. Type boolean.
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_Boolean extends Magento_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * Process argument value
     *
     * @param array $argument
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);
        return filter_var($argument['value'], FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        parent::_validate($argument);
        if (!in_array($argument['value'], array('true', 'false'))) {
            throw new InvalidArgumentException('Value is not boolean argument. ' . $this->_getArgumentInfo($argument));
        }
    }
}
