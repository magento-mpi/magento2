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
     * Return option array of given option model
     * @param string $value
     * @throws InvalidArgumentException
     * @return Magento_Core_Model_Abstract|boolean
     */
    public function process($value)
    {
        /** @var $valueInstance Magento_Core_Model_Option_ArrayInterface */
        $valueInstance = $this->_objectManager->create($value, array());
        if (false === ($valueInstance instanceof Magento_Core_Model_Option_ArrayInterface)) {
            throw new InvalidArgumentException('Incorrect option model');
        }
        $options = $valueInstance->toOptionArray();
        $output = array();
        foreach ($options as $value => $label) {
            $output[] = array('value' => $value, 'label' => $label);
        }
        return $output;
    }
}
