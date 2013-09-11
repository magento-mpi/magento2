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
namespace Magento\ProductAlert\Controller;

class Unsubscribe extends \Magento\Core\Controller\Front\Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!\Mage::getSingleton('Magento\Customer\Model\Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!\Mage::getSingleton('Magento\Customer\Model\Session')->getBeforeUrl()) {
                \Mage::getSingleton('Magento\Customer\Model\Session')->setBeforeUrl($this->_getRefererUrl());
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
        $session    = \Mage::getSingleton('Magento\Catalog\Model\Session');

        /* @var $session \Magento\Catalog\Model\Session */
        $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            /* @var $product \Magento\Catalog\Model\Product */
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('We can\'t find the product.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = \Mage::getModel('Magento\ProductAlert\Model\Price')
                ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }

            $session->addSuccess(__('You deleted the alert subscription.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function priceAllAction()
    {
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');
        /* @var $session \Magento\Customer\Model\Session */

        try {
            \Mage::getModel('Magento\ProductAlert\Model\Price')->deleteCustomer(
                $session->getCustomerId(),
                \Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess(__('You will no longer receive price alerts for this product.'));
        }
        catch (\Exception $e) {
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

        $session = \Mage::getSingleton('Magento\Catalog\Model\Session');
        /* @var $session \Magento\Catalog\Model\Session */
        $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
        /* @var $product \Magento\Catalog\Model\Product */
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            \Mage::getSingleton('Magento\Customer\Model\Session')->addError(__('The product was not found.'));
            $this->_redirect('customer/account/');
            return ;
        }

        try {
            $model  = \Mage::getModel('Magento\ProductAlert\Model\Stock')
                ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId())
                ->setProductId($product->getId())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId())
                ->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $session->addSuccess(__('You will no longer receive stock alerts for this product.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectUrl($product->getProductUrl());
    }

    public function stockAllAction()
    {
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');
        /* @var $session \Magento\Customer\Model\Session */

        try {
            \Mage::getModel('Magento\ProductAlert\Model\Stock')->deleteCustomer(
                $session->getCustomerId(),
                \Mage::app()->getStore()->getWebsiteId()
            );
            $session->addSuccess(__('You will no longer receive stock alerts.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
