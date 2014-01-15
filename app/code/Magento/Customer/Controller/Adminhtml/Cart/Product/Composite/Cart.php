<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog composite product configuration controller
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Controller\Adminhtml\Cart\Product\Composite;

class Cart extends \Magento\Backend\App\Action
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
     * @return \Magento\Customer\Controller\Adminhtml\Cart\Product\Composite\Cart
     */
    protected function _initData()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            throw new \Magento\Core\Exception(__('No customer ID defined.'));
        }

        $this->_customer = $this->_objectManager->create('Magento\Customer\Model\Customer')
            ->load($customerId);

        $quoteItemId = (int) $this->getRequest()->getParam('id');
        $websiteId = (int) $this->getRequest()->getParam('website_id');

        $this->_quote = $this->_objectManager->create('Magento\Sales\Model\Quote')
            ->setWebsite($this->_objectManager->get('Magento\Core\Model\StoreManagerInterface')->getWebsite($websiteId))
            ->loadByCustomer($this->_customer);

        $this->_quoteItem = $this->_quote->getItemById($quoteItemId);
        if (!$this->_quoteItem) {
            throw new \Magento\Core\Exception(__('Please correct the quote items and try again.'));
        }

        return $this;
    }

    /**
     * Ajax handler to response configuration fieldset of composite product in customer's cart
     *
     * @return \Magento\Customer\Controller\Adminhtml\Cart\Product\Composite\Cart
     */
    public function configureAction()
    {
        $configureResult = new \Magento\Object();
        try {
            $this->_initData();

            $quoteItem = $this->_quoteItem;

            $optionCollection = $this->_objectManager->create('Magento\Sales\Model\Quote\Item\Option')
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

        $this->_objectManager->get('Magento\Catalog\Helper\Product\Composite')
            ->renderConfigureResult($configureResult);
    }

    /**
     * IFrame handler for submitted configuration for quote item
     *
     * @return \Magento\Customer\Controller\Adminhtml\Cart\Product\Composite\Cart
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
        $this->_objectManager->get('Magento\Backend\Model\Session')->setCompositeProductResult($updateResult);
        $this->_redirect('catalog/product/showUpdateResult');
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
