<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise checkout index controller
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Controller_Sku extends Magento_Core_Controller_Front_Action
{

    /**
     * Check functionality is enabled and applicable to the Customer
     *
     * @return Enterprise_Checkout_IndexController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // guest redirected to "Login or Create an Account" page
        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Mage_Customer_Model_Session');
        if (!$customerSession->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return $this;
        }

        /** @var $helper Enterprise_Checkout_Helper_Data */
        $helper = Mage::helper('Enterprise_Checkout_Helper_Data');
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
        $this->_initLayoutMessages('Mage_Customer_Model_Session');
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::helper('Enterprise_Checkout_Helper_Data')->__('Order by SKU'));
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
        /** @var $helper Enterprise_Checkout_Helper_Data */
        $helper = Mage::helper('Enterprise_Checkout_Helper_Data');
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
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }
}
