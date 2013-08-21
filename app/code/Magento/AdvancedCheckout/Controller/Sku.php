<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise checkout index controller
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Controller_Sku extends Magento_Core_Controller_Front_Action
{

    /**
     * Check functionality is enabled and applicable to the Customer
     *
     * @return Magento_AdvancedCheckout_IndexController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // guest redirected to "Login or Create an Account" page
        /** @var $customerSession Magento_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');
        if (!$customerSession->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return $this;
        }

        /** @var $helper Magento_AdvancedCheckout_Helper_Data */
        $helper = Mage::helper('Magento_AdvancedCheckout_Helper_Data');
        if (!$helper->isSkuEnabled() || !$helper->isSkuApplied()) {
            $this->_redirect('customer/account');
        }

        return $this;
    }

    /**
     * View Order by SKU page in 'My Account' section
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(__('Order by SKU'));
        }
        $this->renderLayout();
    }

    /**
     * Upload file Action
     *
     * @return void
     */
    public function uploadFileAction()
    {
        /** @var $helper Magento_AdvancedCheckout_Helper_Data */
        $helper = Mage::helper('Magento_AdvancedCheckout_Helper_Data');
        $rows = $helper->isSkuFileUploaded($this->getRequest())
            ? $helper->processSkuFileUploading($this->_getSession())
            : array();

        $items = $this->getRequest()->getPost('items');
        if (!is_array($items)) {
            $items = array();
        }
        foreach ($rows as $row) {
            $items[] = $row;
        }

        $this->getRequest()->setParam('items', $items);
        $this->_forward('advancedAdd', 'cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Magento_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session');
    }
}
