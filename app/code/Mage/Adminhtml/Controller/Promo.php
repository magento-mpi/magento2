<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * sales admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Controller_Promo extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('Mage_CatalogRule::promo');
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Promotions'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Promo'));
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_CatalogRule::promo');
    }

}
