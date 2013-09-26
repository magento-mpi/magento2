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
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
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
            if (is_array($label)) {
                $result[] = $label;
            } else {
                $result[] = array('value' => $value, 'label' => $label);
            }
        }

        return $result;
    }

    /**
     * @param Magento_Core_Model_Layout_Element $argument
     * @return array
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
        parent::_validate($argument);
        $value = $argument['value'];

        if (!isset($value['model'])) {
            throw new InvalidArgumentException(
                'Passed value has incorrect format. ' . $this->_getArgumentInfo($argument)
            );
        }

        if (!is_subclass_of($value['model'], 'Magento_Core_Model_Option_ArrayInterface')) {
            throw new InvalidArgumentException(
                'Incorrect options model. ' . $this->_getArgumentInfo($argument)
            );
        }
    }
}
