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
 * ProductAlert controller
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ProductAlert\Controller;

class Add extends \Magento\Core\Controller\Front\Action
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

    public function testObserverAction()
    {
        $object = new \Magento\Object();
        $observer = \Mage::getSingleton('Magento\ProductAlert\Model\Observer');
        $observer->process($object);
    }

    public function priceAction()
    {
        $session = \Mage::getSingleton('Magento\Catalog\Model\Session');
        $backUrl    = $this->getRequest()->getParam(\Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId()) {
            /* @var $product \Magento\Catalog\Model\Product */
            $session->addError(__('There are not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        try {
            $model  = \Mage::getModel('Magento\ProductAlert\Model\Price')
                ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess(__('You saved the alert subscription.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }

    public function stockAction()
    {
        $session = \Mage::getSingleton('Magento\Catalog\Model\Session');
        /* @var $session \Magento\Catalog\Model\Session */
        $backUrl    = $this->getRequest()->getParam(\Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        if (!$product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId)) {
            /* @var $product \Magento\Catalog\Model\Product */
            $session->addError(__('There are not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = \Mage::getModel('Magento\ProductAlert\Model\Stock')
                ->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(\Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess(__('Alert subscription has been saved.'));
        }
        catch (\Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }
}
