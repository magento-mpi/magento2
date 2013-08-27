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
 * Options handler factory
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Layout_Argument_Handler_OptionsFactory
    implements Magento_Core_Model_Layout_Argument_HandlerFactoryInterface
{
    /**
     * Create options type handler
     * @return Magento_Core_Model_Layout_Argument_HandlerInterface
     */
    public function createHandler()
    {
        return Mage::getModel('Magento_Core_Model_Layout_Argument_Handler_Options');
    }
}
