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
class Magento_ProductAlert_Controller_Add extends Magento_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_objectManager->get('Magento_Customer_Model_Session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if(!$this->_objectManager->get('Magento_Customer_Model_Session')->getBeforeUrl()) {
                $this->_objectManager->get('Magento_Customer_Model_Session')->setBeforeUrl($this->_getRefererUrl());
            }
        }
    }

    public function testObserverAction()
    {
        $object = new Magento_Object();
        $observer = $this->_objectManager->get('Magento_ProductAlert_Model_Observer');
        $observer->process($object);
    }

    public function priceAction()
    {
        $session = $this->_objectManager->get('Magento_Catalog_Model_Session');
        $backUrl    = $this->getRequest()->getParam(Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        $product = $this->_objectManager->create('Magento_Catalog_Model_Product')->load($productId);
        if (!$product->getId()) {
            /* @var $product Magento_Catalog_Model_Product */
            $session->addError(__('There are not enough parameters.'));
            if ($this->_isUrlInternal($backUrl)) {
                $this->_redirectUrl($backUrl);
            } else {
                $this->_redirect('/');
            }
            return ;
        }

        try {
            $model = $this->_objectManager->create('Magento_ProductAlert_Model_Price')
                ->setCustomerId($this->_objectManager->get('Magento_Customer_Model_Session')->getId())
                ->setProductId($product->getId())
                ->setPrice($product->getFinalPrice())
                ->setWebsiteId(
                    $this->_objectManager->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getWebsiteId()
                );
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
        $session = $this->_objectManager->get('Magento_Catalog_Model_Session');
        /* @var $session Magento_Catalog_Model_Session */
        $backUrl    = $this->getRequest()->getParam(Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED);
        $productId  = (int) $this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return ;
        }

        if (!$product = $this->_objectManager->create('Magento_Catalog_Model_Product')->load($productId)) {
            /* @var $product Magento_Catalog_Model_Product */
            $session->addError(__('There are not enough parameters.'));
            $this->_redirectUrl($backUrl);
            return ;
        }

        try {
            $model = $this->_objectManager->create('Magento_ProductAlert_Model_Stock')
                ->setCustomerId($this->_objectManager->get('Magento_Customer_Model_Session')->getId())
                ->setProductId($product->getId())
                ->setWebsiteId(
                    $this->_objectManager->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getWebsiteId()
                );
            $model->save();
            $session->addSuccess(__('Alert subscription has been saved.'));
        }
        catch (Exception $e) {
            $session->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->_redirectReferer();
    }
}
