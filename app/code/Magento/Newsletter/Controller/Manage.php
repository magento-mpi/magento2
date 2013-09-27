<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customers newsletter subscription controller
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Newsletter_Controller_Manage extends Magento_Core_Controller_Front_Action
{
    /**
     * Customer session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Customer_Model_Session $customerSession
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Customer_Model_Session $customerSession
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_customerSession->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $this->_initLayoutMessages('Magento_Catalog_Model_Session');

        if ($block = $this->getLayout()->getBlock('customer_newsletter')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->getLayout()->getBlock('head')->setTitle(__('Newsletter Subscription'));
        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!$this->_validateFormKey()) {
            return $this->_redirect('customer/account/');
        }
        try {
            $this->_customerSession->getCustomer()
                ->setStoreId($this->_storeManager->getStore()->getId())
                ->setIsSubscribed((boolean)$this->getRequest()->getParam('is_subscribed', false))
                ->save();
            if ((boolean)$this->getRequest()->getParam('is_subscribed', false)) {
                $this->_customerSession->addSuccess(__('We saved the subscription.'));
            } else {
                $this->_customerSession->addSuccess(__('We removed the subscription.'));
            }
        }
        catch (Exception $e) {
            $this->_customerSession->addError(__('Something went wrong while saving your subscription.'));
        }
        $this->_redirect('customer/account/');
    }
}
