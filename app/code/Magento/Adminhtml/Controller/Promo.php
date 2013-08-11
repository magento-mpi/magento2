<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * sales admin controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Promo extends Magento_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Magento_CatalogRule::promo');
        $this->_addBreadcrumb(Mage::helper('Magento_Adminhtml_Helper_Data')->__('Promotions'), Mage::helper('Magento_Adminhtml_Helper_Data')->__('Promo'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_CatalogRule::promo');
    }

}
