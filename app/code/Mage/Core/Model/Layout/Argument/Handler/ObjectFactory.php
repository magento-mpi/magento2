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
 * Object handler factory
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Layout_Argument_Handler_ObjectFactory
    implements Mage_Core_Model_Layout_Argument_HandlerFactoryInterface
{
    /**
     * Create object type handler
     * @return Mage_Core_Model_Layout_Argument_HandlerInterface
     */
    public function createHandler()
    {
        return Mage::getModel('Mage_Core_Model_Layout_Argument_Handler_Object', array(
            'objectFactory' => Mage::app()->getConfig()
        ));
    }
}
