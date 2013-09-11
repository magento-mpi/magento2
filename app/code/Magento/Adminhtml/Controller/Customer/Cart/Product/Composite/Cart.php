<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog composite product configuration controller
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Controller\Customer\Cart\Product\Composite;

class Cart extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Customer we're working with
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer = null;

    /**
     * Quote we're working with
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote = null;

    /**
     * Quote item we're working with
     *
     * @var \Magento\Sales\Model\Quote\Item
     */
    protected $_quoteItem = null;

    /**
     * Loads customer, quote and quote item by request params
     *
     * @return \Magento\Adminhtml\Controller\Customer\Cart\Product\Composite\Cart
     */
    protected function _initData()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            \Mage::throwException(__('No customer ID defined.'));
        }

        $this->_customer = \Mage::getModel('Magento\Customer\Model\Customer')
            ->load($customerId);

        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');

        $this->_quote = \Mage::getModel('Magento\Sales\Model\Quote')
            ->setWebsite(\Mage::app()->getWebsite($websiteId))
            ->loadByCustomer($this->_customer);

        $this->_quoteItem = $this->_quote->getItemById($quoteItemId);
        if (!$this->_quoteItem) {
            \Mage::throwException(__('Please correct the quote items and try again.'));
        }

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     *
     * @return \Magento\Adminhtml\Controller\Customer\Cart\Product\Composite\Cart
     */
    public function configureAction()
    {
        $configureResult = new \Magento\Object();
        try {
            $this->_initData();

            $quoteItem = $this->_quoteItem;

            $optionCollection = \Mage::getModel('Magento\Sales\Model\Quote\Item\Option')
                ->getCollection()
                ->addItemFilter($quoteItem);
            $quoteItem->setOptions($optionCollection->getOptionsByItem($quoteItem));

            $configureResult->setOk(true);
            $configureResult->setProductId($quoteItem->getProductId());
            $configureResult->setBuyRequest($quoteItem->getBuyRequest());
            $configureResult->setCurrentStoreId($quoteItem->getStoreId());
            $configureResult->setCurrentCustomer($this->_customer);
        } catch (\Exception $e) {
            $configureResult->setError(true);
            $configureResult->setMessage($e->getMessage());
        }

        /* @var $helper \Magento\Adminhtml\Helper\Catalog\Product\Composite */
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Catalog\Product\Composite');
        $helper->renderConfigureResult($this, $configureResult);

        return $this;
    }

    /**
     * IFrame handler for submitted configuration for quote item
     *
     * @return \Magento\Adminhtml\Controller\Customer\Cart\Product\Composite\Cart
     */
    public function updateAction()
    {
        $updateResult = new \Magento\Object();
        try {
            $this->_initData();

            $buyRequest = new \Magento\Object($this->getRequest()->getParams());
            $this->_quote->updateItem($this->_quoteItem->getId(), $buyRequest);
            $this->_quote->collectTotals()
                ->save();

            $updateResult->setOk(true);
        } catch (\Exception $e) {
            $updateResult->setError(true);
            $updateResult->setMessage($e->getMessage());
        }

        $updateResult->setJsVarName($this->getRequest()->getParam('as_js_varname'));
        \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setCompositeProductResult($updateResult);
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
        return $this->_authorization->isAllowed('Magento_Customer::manage');
    }
}
