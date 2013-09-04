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
     * Parse argument node
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array
     */
    public function parse(Magento_Core_Model_Layout_Element $argument)
    {
        $result = parent::parse($argument);
        $result = array_merge_recursive($result, array(
            'value' => $this->_getArgumentValue($argument),
        ));

        return $result;
    }

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
        if (!isset($argument['value'])) {
            throw new InvalidArgumentException('Value is required for boolean argument');
        }

        if (!in_array($argument['value'], array('true', 'false'))) {
            throw new InvalidArgumentException('Value is not boolean argument');
        }
    }
}
