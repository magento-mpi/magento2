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
namespace Magento\Sendfriend\Controller;

class Product extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Predispatch: check is enable module
     * If allow only for customer - redirect to login page
     *
     * @return \Magento\Sendfriend\Controller\Product
     */
    public function preDispatch()
    {
        parent::preDispatch();

        /* @var $helper \Magento\Sendfriend\Helper\Data */
        $helper = $this->_objectManager->get('Magento\Sendfriend\Helper\Data');
        /* @var $session \Magento\Customer\Model\Session */
        $session = \Mage::getSingleton('Magento\Customer\Model\Session');

        if (!$helper->isEnabled()) {
            $this->norouteAction();
            return $this;
        }

        if (!$helper->isAllowForGuest() && !$session->authenticate($this)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            if ($this->getRequest()->getActionName() == 'sendemail') {
                $session->setBeforeAuthUrl(\Mage::getUrl('*/*/send', array(
                    '_current' => true
                )));
                \Mage::getSingleton('Magento\Catalog\Model\Session')
                    ->setSendfriendFormData($this->getRequest()->getPost());
            }
        }

        return $this;
    }

    /**
     * Initialize Product Instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function _initProduct()
    {
        $productId  = (int)$this->getRequest()->getParam('id');
        if (!$productId) {
            return false;
        }
        $product = \Mage::getModel('Magento\Catalog\Model\Product')
            ->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            return false;
        }

        $this->_coreRegistry->register('product', $product);
        return $product;
    }

    /**
     * Initialize send friend model
     *
     * @return \Magento\Sendfriend\Model\Sendfriend
     */
    protected function _initSendToFriendModel()
    {
        $model  = \Mage::getModel('Magento\Sendfriend\Model\Sendfriend');
        $model->setRemoteAddr($this->_objectManager->get('Magento\Core\Helper\Http')->getRemoteAddr(true));
        $model->setCookie(Mage::app()->getCookie());
        $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId());

        $this->_coreRegistry->register('send_to_friend_model', $model);

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
            \Mage::getSingleton('Magento\Catalog\Model\Session')->addNotice(
                __('You can\'t send messages more than %1 times an hour.', $model->getMaxSendsToFriend())
            );
        }

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Catalog\Model\Session');

        $this->_eventManager->dispatch('sendfriend_product', array('product' => $product));
        $data = \Mage::getSingleton('Magento\Catalog\Model\Session')->getSendfriendFormData();
        if ($data) {
            \Mage::getSingleton('Magento\Catalog\Model\Session')->setSendfriendFormData(true);
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
            $category = \Mage::getModel('Magento\Catalog\Model\Category')
                ->load($categoryId);
            $product->setCategory($category);
            $this->_coreRegistry->register('current_category', $category);
        }

        $model->setSender($this->getRequest()->getPost('sender'));
        $model->setRecipients($this->getRequest()->getPost('recipients'));
        $model->setProduct($product);

        try {
            $validate = $model->validate();
            if ($validate === true) {
                $model->send();
                \Mage::getSingleton('Magento\Catalog\Model\Session')->addSuccess(__('The link to a friend was sent.'));
                $this->_redirectSuccess($product->getProductUrl());
                return;
            }
            else {
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        \Mage::getSingleton('Magento\Catalog\Model\Session')->addError($errorMessage);
                    }
                }
                else {
                    \Mage::getSingleton('Magento\Catalog\Model\Session')->addError(__('We found some problems with the data.'));
                }
            }
        }
        catch (\Magento\Core\Exception $e) {
            \Mage::getSingleton('Magento\Catalog\Model\Session')->addError($e->getMessage());
        }
        catch (\Exception $e) {
            \Mage::getSingleton('Magento\Catalog\Model\Session')
                ->addException($e, __('Some emails were not sent.'));
        }

        // save form data
        \Mage::getSingleton('Magento\Catalog\Model\Session')->setSendfriendFormData($data);

        $this->_redirectError(\Mage::getURL('*/*/send', array('_current' => true)));
    }
}
