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
 * Layout argument. Type options
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_Options extends Magento_Core_Model_Layout_Argument_HandlerAbstract
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
     * Process Option argument
     *
     * @param array $argument
     * @return string
     * @throws InvalidArgumentException
     */
    public function process(array $argument)
    {
        $this->_validate($argument);

        $optionsModel = $this->_objectManager->create($argument['value']['model']);

        $options = $optionsModel->toOptionArray();
        $result = array();

        foreach ($options as $value => $label) {
            $result[] = array('value' => $value, 'label' => $label);
        }

        return $result;
    }

    /**
     * @param Magento_Core_Model_Layout_Element $argument
     * @return string
     */
    protected function _getArgumentValue(Magento_Core_Model_Layout_Element $argument)
    {
        return array('model' => (string)$argument['model']);
    }

    /**
     * @param array $argument
     * @throws InvalidArgumentException
     */
    protected function _validate(array $argument)
    {
        if (!isset($argument['value'])) {
            throw new InvalidArgumentException('Value is required for options argument');
        }
        $value = $argument['value'];

        if (!isset($value['model'])) {
            throw new InvalidArgumentException('Passed value has incorrect format');
        }

        if (!is_subclass_of($value['model'], 'Magento_Core_Model_Option_ArrayInterface')) {
            throw new InvalidArgumentException('Incorrect options model');
        }
    }
}
