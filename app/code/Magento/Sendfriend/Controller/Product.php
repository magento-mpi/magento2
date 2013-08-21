<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sendfriend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Email to a Friend Product Controller
 *
 * @category    Magento
 * @package     Magento_Sedfriend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sendfriend_Controller_Product extends Magento_Core_Controller_Front_Action
{
    /**
     * Predispatch: check is enable module
     * If allow only for customer - redirect to login page
     *
     * @return Magento_Sendfriend_Controller_Product
     */
    public function preDispatch()
    {
        parent::preDispatch();

        /* @var $helper Magento_Sendfriend_Helper_Data */
        $helper = Mage::helper('Magento_Sendfriend_Helper_Data');
        /* @var $session Magento_Customer_Model_Session */
        $session = Mage::getSingleton('Magento_Customer_Model_Session');

        if (!$helper->isEnabled()) {
            $this->norouteAction();
            return $this;
        }

        if (!$helper->isAllowForGuest() && !$session->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            if ($this->getRequest()->getActionName() == 'sendemail') {
                $session->setBeforeAuthUrl(Mage::getUrl('*/*/send', array(
                    '_current' => true
                )));
                Mage::getSingleton('Magento_Catalog_Model_Session')
                    ->setSendfriendFormData($this->getRequest()->getPost());
            }
        }

        return $this;
    }

    /**
     * Initialize Product Instance
     *
     * @return Magento_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $productId  = (int)$this->getRequest()->getParam('id');
        if (!$productId) {
            return false;
        }
        $product = Mage::getModel('Magento_Catalog_Model_Product')
            ->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            return false;
        }

        Mage::register('product', $product);
        return $product;
    }

    /**
     * Initialize send friend model
     *
     * @return Magento_Sendfriend_Model_Sendfriend
     */
    protected function _initSendToFriendModel()
    {
        $model  = Mage::getModel('Magento_Sendfriend_Model_Sendfriend');
        $model->setRemoteAddr(Mage::helper('Magento_Core_Helper_Http')->getRemoteAddr(true));
        $model->setCookie(Mage::app()->getCookie());
        $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        Mage::register('send_to_friend_model', $model);

        return $model;
    }

    /**
     * Show Send to a Friend Form
     *
     */
    public function sendAction()
    {
        $product    = $this->_initProduct();
        $model      = $this->_initSendToFriendModel();

        if (!$product) {
            $this->_forward('noRoute');
            return;
        }

        if ($model->getMaxSendsToFriend() && $model->isExceedLimit()) {
            Mage::getSingleton('Magento_Catalog_Model_Session')->addNotice(
                __('You can\'t send messages more than %1 times an hour.', $model->getMaxSendsToFriend())
            );
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

        $this->_eventManager->dispatch('sendfriend_product', array('product' => $product));
        $data = Mage::getSingleton('Magento_Catalog_Model_Session')->getSendfriendFormData();
        if ($data) {
            Mage::getSingleton('Magento_Catalog_Model_Session')->setSendfriendFormData(true);
            $block = $this->getLayout()->getBlock('sendfriend.send');
            if ($block) {
                $block->setFormData($data);
            }
        }

        $this->renderLayout();
    }

    /**
     * Send Email Post Action
     *
     */
    public function sendmailAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('*/*/send', array('_current' => true));
        }

        $product    = $this->_initProduct();
        $model      = $this->_initSendToFriendModel();
        $data       = $this->getRequest()->getPost();

        if (!$product || !$data) {
            $this->_forward('noRoute');
            return;
        }

        $categoryId = $this->getRequest()->getParam('cat_id', null);
        if ($categoryId) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')
                ->load($categoryId);
            $product->setCategory($category);
            Mage::register('current_category', $category);
        }

        $model->setSender($this->getRequest()->getPost('sender'));
        $model->setRecipients($this->getRequest()->getPost('recipients'));
        $model->setProduct($product);

        try {
            $validate = $model->validate();
            if ($validate === true) {
                $model->send();
                Mage::getSingleton('Magento_Catalog_Model_Session')->addSuccess(__('The link to a friend was sent.'));
                $this->_redirectSuccess($product->getProductUrl());
                return;
            }
            else {
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        Mage::getSingleton('Magento_Catalog_Model_Session')->addError($errorMessage);
                    }
                }
                else {
                    Mage::getSingleton('Magento_Catalog_Model_Session')->addError(__('We found some problems with the data.'));
                }
            }
        }
        catch (Magento_Core_Exception $e) {
            Mage::getSingleton('Magento_Catalog_Model_Session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('Magento_Catalog_Model_Session')
                ->addException($e, __('Some emails were not sent.'));
        }

        // save form data
        Mage::getSingleton('Magento_Catalog_Model_Session')->setSendfriendFormData($data);

        $this->_redirectError(Mage::getURL('*/*/send', array('_current' => true)));
    }
}
