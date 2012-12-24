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
 * Forward controller
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Controller_Varien_Action_Forward extends Mage_Core_Controller_Varien_ActionAbstract
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
