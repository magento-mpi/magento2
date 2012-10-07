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
 * Controller dispatchable interface
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Core_Controller_Varien_DispatchableInterface
{
    /**
     * Dispatch controller action
     *
     * @abstract
     * @param string $action action name
     * @return void
     */
    public function dispatch($action);
}
