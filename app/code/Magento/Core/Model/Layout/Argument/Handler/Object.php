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
 * Layout argument. Type object
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_Object extends Magento_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

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
            'value' => $this->_getArgumentValue($argument)
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
        $value = $argument['value'];

        return $this->_objectManager->create($value['object']);
    }

    /**
     * Validate argument
     * @param $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        if (!isset($argument['value'])) {
            throw new InvalidArgumentException('Value is required for object argument');
        }
        $value = $argument['value'];

        if (!isset($value['object'])) {
            throw new InvalidArgumentException('Passed value has incorrect format');
        }

        if (!is_subclass_of($value['object'], 'Magento_Data_Collection')) {
            throw new InvalidArgumentException('Incorrect data source model');
        }
    }

    /**
     * Retrieve value from argument
     *
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        return array(
            'object' => parent::_getArgumentValue($argument)
        );
    }
}
