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
 * Layout argument. Type object
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Handler_Object extends Mage_Core_Model_Layout_Argument_HandlerAbstract
{
    /**
     * Instantiate model object
     * @param string $value
     * @return Mage_Core_Model_Abstract|boolean
     */
    public function process($value)
    {
        $valueInstance = $this->_objectFactory->getModelInstance($value);
        return $valueInstance;
    }
}
