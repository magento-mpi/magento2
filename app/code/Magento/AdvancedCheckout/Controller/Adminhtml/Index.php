<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml;

use Magento\AdvancedCheckout\Exception as AdvancedCheckoutException;
use Magento\Backend\App\Action;
use Magento\Framework\Model\Exception;

/**
 * Admin Checkout index controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Flag that indicates whether page must be reloaded with correct params or not
     *
     * @var bool
     */
    protected $_redirectFlag = false;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $registry)
    {
        parent::__construct($context);
        $this->_registry = $registry;
    }

    /**
     * Return Checkout model as singleton
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function getCartModel()
    {
        return $this->_objectManager->get(
            'Magento\AdvancedCheckout\Model\Cart'
        )->setSession(
            $this->_objectManager->get('Magento\Backend\Model\Session')
        )->setContext(
            \Magento\AdvancedCheckout\Model\Cart::CONTEXT_ADMIN_CHECKOUT
        )->setCurrentStore(
            $this->getRequest()->getPost('store')
        );
    }

    /**
     * Init store based on quote and customer sharing options
     * Store customer, store and quote to registry
     *
     * @param bool $useRedirects
     *
     * @return $this
     * @throws AdvancedCheckoutException
     */
    protected function _initData($useRedirects = true)
    {
        $customerId = $this->getRequest()->getParam('customer');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        if (!$customer->getId()) {
            throw new AdvancedCheckoutException(__('Customer not found'));
        }

        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        if ($storeManager->getStore(
            \Magento\Store\Model\Store::ADMIN_CODE
        )->getWebsiteId() == $customer->getWebsiteId()
        ) {
            if ($useRedirects) {
                $this->messageManager->addError(__('Shopping cart management disabled for this customer.'));
                $this->_redirect('customer/index/edit', array('id' => $customer->getId()));
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new AdvancedCheckoutException(__('Shopping cart management is disabled for this customer.'));
            }
        }

        $cart = $this->getCartModel();
        $cart->setCustomer($customer);

        $storeId = $this->getRequest()->getParam('store');

        if ($storeId === null || $storeId == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            $storeId = $cart->getPreferredStoreId();
            if ($storeId && $useRedirects) {
                // Redirect to preferred store view
                if ($this->getRequest()->getQuery('isAjax', false) || $this->getRequest()->getQuery('ajax', false)) {
                    $this->getResponse()->representJson(
                        $this->_objectManager->get(
                            'Magento\Core\Helper\Data'
                        )->jsonEncode(
                            array(
                                'url' => $this->getUrl(
                                    '*/*/index',
                                    array('store' => $storeId, 'customer' => $customerId)
                                )
                            )
                        )
                    );
                } else {
                    $this->_redirect('checkout/*/index', array('store' => $storeId, 'customer' => $customerId));
                }
                $this->_redirectFlag = true;
                return $this;
            } else {
                throw new AdvancedCheckoutException(__('We could not find this store.'));
            }
        } else {
            // try to find quote for selected store
            $cart->setStoreId($storeId);
        }

        $quote = $cart->getQuote();

        // Currency init
        if ($quote->getId()) {
            $quoteCurrencyCode = $quote->getData('quote_currency_code');
            if ($quoteCurrencyCode != $storeManager->getStore($storeId)->getCurrentCurrencyCode()) {
                $quoteCurrency = $this->_objectManager->create(
                    'Magento\Directory\Model\Currency'
                )->load(
                    $quoteCurrencyCode
                );
                $quote->setForcedCurrency($quoteCurrency);
                $storeManager->getStore($storeId)->setCurrentCurrencyCode($quoteCurrency->getCode());
            }
        } else {
            // customer and addresses should be set to resolve situation when no quote was saved for customer previously
            // otherwise quote would be saved with customer_id = null and zero totals
            $quote->setStore($storeManager->getStore($storeId))->setCustomer($customer);
            $quote->getBillingAddress();
            $quote->getShippingAddress();
            $this->_objectManager->get('Magento\Sales\Model\QuoteRepository')->save($quote);
        }

        $this->_registry->register('checkout_current_quote', $quote);
        $this->_registry->register('checkout_current_customer', $customer);
        $this->_registry->register('checkout_current_store', $storeManager->getStore($storeId));

        return $this;
    }

    /**
     * Renderer for page title
     *
     * @return $this
     */
    protected function _initTitle()
    {
        $title = $this->_view->getPage()->getConfig()->getTitle();
        $title->prepend(__('Customers'));
        $title->prepend(__('Customers'));
        $customer = $this->_registry->registry('checkout_current_customer');
        if ($customer) {
            $title->prepend($customer->getName());
        }
        $itemsBlock = $this->_view->getLayout()->getBlock('ID');
        if (is_object($itemsBlock) && is_callable([$itemsBlock, 'getHeaderText'])) {
            $title->prepend($itemsBlock->getHeaderText());
        } else {
            $title->prepend(__('Shopping Cart'));
        }
        return $this;
    }

    /**
     * Process exceptions in ajax requests
     *
     * @param \Exception $e
     * @return void
     */
    protected function _processException(\Exception $e)
    {
        if ($e instanceof Exception) {
            $result = array('error' => $e->getMessage());
        } elseif ($e instanceof \Exception) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $result = array('error' => __('An error has occurred. See error log for details.'));
        }
        $this->getResponse()->representJson($this->_objectManager->get('Magento\Core\Helper\Data')->jsonEncode($result));
    }

    /**
     * Acl check for quote modifications
     *
     * @return void
     * @throws Exception
     */
    protected function _isModificationAllowed()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            throw new Exception(__('You do not have access to this.'));
        }
    }

    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Magento_AdvancedCheckout::view'
        ) || $this->_authorization->isAllowed(
            'Magento_AdvancedCheckout::update'
        );
    }
}
