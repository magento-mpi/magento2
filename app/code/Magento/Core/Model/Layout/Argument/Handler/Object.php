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
     * Instantiate model object
     * @param string $value
     * @return Magento_Core_Model_Abstract|boolean
     */
    public function process($value)
    {
        $valueInstance = $this->_objectManager->create($value, array());
        return $valueInstance;
    }
}
