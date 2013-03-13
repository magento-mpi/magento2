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
 * Argument handler factory interface
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Core_Model_Layout_Argument_HandlerFactoryInterface
{
    /**
     * Create concrete handler object
     * @return Mage_Core_Model_Layout_Argument_HandlerInterface
     */
    public function createHandler();
}
