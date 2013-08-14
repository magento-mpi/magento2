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
 * Forward controller
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Controller_Varien_Action_Forward extends Magento_Core_Controller_Varien_ActionAbstract
{
    /**
     * Dispatch controller action
     *
     * @param string $action action name
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function dispatch($action)
    {
        $this->_request->setDispatched(false);
    }
}
