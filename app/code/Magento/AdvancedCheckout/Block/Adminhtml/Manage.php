<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml;

/**
 * Admin Checkout main form container
 */
class Manage extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        $this->_localeCurrency = $localeCurrency;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('checkout_manage_container');
    }

    /**
     * Prepare layout, create buttons
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            return $this;
        }

        if ($this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->getToolbar()->addChild(
                'save',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Create Order'),
                    'onclick' => 'setLocation(\'' . $this->getCreateOrderUrl() . '\');',
                    'class' => 'save primary'
                ]
            );
        }

        $this->getToolbar()->addChild(
            'back',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getBackUrl() . '\');',
                'class' => 'back'
            ]
        );

        $this->addChild(
            'add_products_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add Products'),
                'onclick' => 'checkoutObj.searchProducts()',
                'class' => 'add',
                'id' => 'add_products_btn'
            ]
        );

        $this->addChild(
            'update_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Update Items and Qty\'s'),
                'onclick' => 'checkoutObj.updateItems()',
                'class' => 'update'
            ]
        );
        $deleteAllConfirmString = __('Are you sure you want to clear your shopping cart?');
        $this->addChild(
            'empty_customer_cart_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Clear the shopping cart.'),
                'onclick' => 'confirm(\'' .
                $deleteAllConfirmString .
                '\') ' .
                ' && checkoutObj.updateItems({\'empty_customer_cart\': 1})',
                'class' => 'clear'
            ]
        );

        $this->addChild(
            'addto_cart_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add Selected Product(s) to Shopping Cart'),
                'onclick' => 'checkoutObj.addToCart()',
                'class' => 'add button-to-cart'
            ]
        );

        $this->addChild(
            'cancel_add_products_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Cancel'), 'onclick' => 'checkoutObj.cancelSearch()', 'class' => 'cancel']
        );

        return $this;
    }

    /**
     * Rewrite for getFormHtml()
     *
     * @return string
     */
    public function getFormHtml()
    {
        return '';
    }

    /**
     * Return header title
     *
     * @return string
     */
    public function getHeaderText()
    {
        $customer = $this->escapeHtml($this->_getCustomer()->getName());
        $store = $this->escapeHtml($this->_getStore()->getName());
        return __('Shopping Cart for %1 in %2', $customer, $store);
    }

    /**
     * Return current customer from registry
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->_coreRegistry->registry('checkout_current_customer');
    }

    /**
     * Return current store from registry
     *
     * @return \Magento\Store\Model\Store
     */
    protected function _getStore()
    {
        return $this->_coreRegistry->registry('checkout_current_store');
    }

    /**
     * Return URL to customer edit page
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->_getCustomer() && $this->_getCustomer()->getId()) {
            return $this->getUrl('customer/index/edit', ['id' => $this->_getCustomer()->getId()]);
        } else {
            return $this->getUrl('customer/index');
        }
    }

    /**
     * Return URL to controller action
     *
     * @param string $action
     * @return string
     */
    public function getActionUrl($action)
    {
        return $this->getUrl('checkout/*/' . $action, ['_current' => true]);
    }

    /**
     * Return URL to admin order creation
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('checkout/*/createOrder', ['_current' => true]);
    }

    /**
     * Retrieve url for loading blocks
     *
     * @return string
     */
    public function getLoadBlockUrl()
    {
        return $this->getUrl('checkout/*/loadBlock');
    }

    /**
     * @return string
     */
    public function getOrderDataJson()
    {
        $actionUrls = [
            'cart' => $this->getActionUrl('cart'),
            'applyCoupon' => $this->getActionUrl('applyCoupon'),
            'coupon' => $this->getActionUrl('coupon'),
        ];

        $messages = ['chooseProducts' => __('Choose  products to add to shopping cart.')];

        $data = [
            'action_urls' => $actionUrls,
            'messages' => $messages,
            'customer_id' => $this->_getCustomer()->getId(),
            'store_id' => $this->_getStore()->getId(),
        ];

        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Retrieve currency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->_localeCurrency->getCurrency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * Retrieve current order currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_getStore()->getCurrentCurrencyCode();
    }
}
