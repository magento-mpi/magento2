<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog composite product configuration controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Customer_Cart_Product_Composite_CartController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Customer we're working with
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Quote we're working with
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * Quote item we're working with
     *
     * @var Mage_Sales_Model_Quote_Item
     */
    protected $_quoteItem = null;

    /**
     * Loads customer, quote and quote item by request params
     *
     * @return Mage_Adminhtml_Customer_Cart_Product_Composite_CartController
     */
    protected function _initData()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            Mage::throwException($this->__('No customer ID defined.'));
        }

        $this->_customer = Mage::getModel('Mage_Customer_Model_Customer')
            ->load($customerId);

        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');

        $this->_quote = Mage::getModel('Mage_Sales_Model_Quote')
            ->setWebsite(Mage::app()->getWebsite($websiteId))
            ->loadByCustomer($this->_customer);

        $this->_quoteItem = $this->_quote->getItemById($quoteItemId);
        if (!$this->_quoteItem) {
            Mage::throwException($this->__('Please correct the quote items and try again.'));
        }

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     *
     * @return Mage_Adminhtml_Customer_Cart_Product_Composite_CartController
     */
    public function configureAction()
    {
        $configureResult = new Varien_Object();
        try {
            $this->_initData();

            $quoteItem = $this->_quoteItem;

            $optionCollection = Mage::getModel('Mage_Sales_Model_Quote_Item_Option')
                ->getCollection()
                ->addItemFilter($quoteItem);
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setOk(true);
            $configureResult->setProductId($quoteItem->getProductId());
            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setCurrentCustomer($this->_customer);
        } catch (Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        /* @var $helper Mage_Adminhtml_Helper_Catalog_Product_Composite */
        $helper = Mage::helper('Mage_Adminhtml_Helper_Catalog_Product_Composite');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * IFrame handler for submitted configuration for quote item
     *
     * @return Mage_Adminhtml_Customer_Cart_Product_Composite_CartController
     */
    public function updateAction()
    {
        $updateResult = new Varien_Object();
        try {
            $this->_initData();

            $buyRequest = new Varien_Object($this->getRequest()->getParams());
            $this->_quote->updateItem($this->_quoteItem->getId(), $buyRequest);
            $this->_quote->collectTotals()
                ->save();

            $updateResult->setOk(true);
        } catch (Exception $e) {
            $updateResult->setError(true);
            $updateResult->setMessage($e->getMessage());
        }

        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        Mage::getSingleton('Mage_Adminhtml_Model_Session')->setCompositeProductResult($updateResult);
        $this->_redirect('*/catalog_product/showUpdateResult');

        return $this;
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Customer::manage');
    }
}
