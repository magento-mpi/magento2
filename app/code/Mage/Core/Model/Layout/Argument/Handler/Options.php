<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout argument. Type options
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Handler_Options extends Mage_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * Return option array of given option model
     * @param string $value
     * @throws InvalidArgumentException
     * @return Mage_Core_Model_Abstract|boolean
     */
    public function process($value)
    {
        /** @var $valueInstance Mage_Core_Model_Option_ArrayInterface */
        $valueInstance = $this->_objectManager->create($value, array());
        if (false === ($valueInstance instanceof Mage_Core_Model_Option_ArrayInterface)) {
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
