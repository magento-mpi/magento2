<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders controller
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Sales_Controller_Order extends Magento_Sales_Controller_Abstract
{

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = $this->_objectManager->get('Magento_Customer_Helper_Data')->getLoginUrl();

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * Customer order history
     */
    public function historyAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

        $this->getLayout()->getBlock('head')->setTitle(__('My Orders'));

        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }
}
