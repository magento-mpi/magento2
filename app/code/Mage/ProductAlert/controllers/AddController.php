<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ProductAlert controller
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_AddController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('Mage_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!Mage::getSingleton('Mage_Customer_Model_Session')->getBeforeUrl()) {
                Mage::getSingleton('Mage_Customer_Model_Session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }

    public function testObserverAction()
    {
        $object = new Varien_Object();
        $observer = Mage::getSingleton('Mage_ProductAlert_Model_Observer');
        $observer->process($object);
    }

    public function priceAction()
    {
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        $backUrl    = $this->getRequest()->getParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
        if (!$product->getId()) {
            /* @var $product Mage_Catalog_Model_Product */
            $session->addError(__('There are not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        try {
            $model  = Mage::getModel('Mage_ProductAlert_Model_Price')
                ->setCustomerId(Mage::getSingleton('Mage_Customer_Model_Session')->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess(__('You saved the alert subscription.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }

    public function stockAction()
    {
        $session = Mage::getSingleton('Mage_Catalog_Model_Session');
        /* @var $session Mage_Catalog_Model_Session */
        $backUrl    = $this->getRequest()->getParam(Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        if (!$product = Mage::getModel('Mage_Catalog_Model_Product')->load($productId)) {
            /* @var $product Mage_Catalog_Model_Product */
            $session->addError(__('There are not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = Mage::getModel('Mage_ProductAlert_Model_Stock')
                ->setCustomerId(Mage::getSingleton('Mage_Customer_Model_Session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $model->save();
            $session->addSuccess(__('Alert subscription has been saved.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }
}
