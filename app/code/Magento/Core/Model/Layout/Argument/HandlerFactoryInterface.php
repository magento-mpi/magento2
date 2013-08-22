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
 * Argument handler factory interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Core_Model_Layout_Argument_HandlerFactoryInterface
{
    /**
     * Create concrete handler object
     * @return Magento_Core_Model_Layout_Argument_HandlerInterface
     */
    public function createHandler();
}
