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
 * Controller dispatchable interface
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Core_Controller_Varien_DispatchableInterface
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
