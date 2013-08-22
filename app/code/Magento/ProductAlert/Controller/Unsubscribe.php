<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * ProductAlert unsubscribe controller
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_ProductAlert_Controller_Unsubscribe extends Magento_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!Mage::getSingleton('Magento_Customer_Model_Session')->getBeforeUrl()) {
                Mage::getSingleton('Magento_Customer_Model_Session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }

    public function priceAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }
        $session    = Mage::getSingleton('Magento_Catalog_Model_Session');

        /* @var $session Magento_Catalog_Model_Session */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            /* @var $product Magento_Catalog_Model_Product */
            Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('We can\'t find the product.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('Magento_ProductAlert_Model_Price')
                ->setCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }

            $session->addSuccess(__('You deleted the alert subscription.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function priceAllAction()
    {
        $session = Mage::getSingleton('Magento_Customer_Model_Session');
        /* @var $session Magento_Customer_Model_Session */

        try {
            Mage::getModel('Magento_ProductAlert_Model_Price')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess(__('You will no longer receive price alerts for this product.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }

    public function stockAction()
    {
        $productId  = (int) $this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        $session = Mage::getSingleton('Magento_Catalog_Model_Session');
        /* @var $session Magento_Catalog_Model_Session */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($productId);
        /* @var $product Magento_Catalog_Model_Product */
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            Mage::getSingleton('Magento_Customer_Model_Session')->addError(__('The product was not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = Mage::getModel('Magento_ProductAlert_Model_Stock')
                ->setCustomerId(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $session->addSuccess(__('You will no longer receive stock alerts for this product.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function stockAllAction()
    {
        $session = Mage::getSingleton('Magento_Customer_Model_Session');
        /* @var $session Magento_Customer_Model_Session */

        try {
            Mage::getModel('Magento_ProductAlert_Model_Stock')->deleteCustomer(
                $session->getCustomerId(),
                Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess(__('You will no longer receive stock alerts.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
