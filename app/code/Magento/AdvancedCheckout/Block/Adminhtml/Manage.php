<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('checkout_manage_container');

        if ($this->_authorization->isAllowed('Magento_Sales::create')) {
            $this->_updateButton('save', 'label', __('Create Order'));
            $this->_updateButton('save', 'onclick', 'setLocation(\'' . $this->getCreateOrderUrl() . '\');');
        } else {
            $this->_removeButton('save');
        }
        $this->_removeButton('reset');
        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\');');
    }

    /**
     * Prepare layout, create buttons
     *
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            return $this;
        }

        $this->addChild('add_products_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Add Products'),
            'onclick' => 'checkoutObj.searchProducts()',
            'class' => 'add',
            'id' => 'add_products_btn'
        ));

        $this->addChild('update_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Update Items and Qty\'s'),
            'onclick' => 'checkoutObj.updateItems()',
            'class' => 'update'
        ));
        $deleteAllConfirmString = __('Are you sure you want to clear your shopping cart?');
        $this->addChild('empty_customer_cart_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Clear the shopping cart.'),
            'onclick' => 'confirm(\'' . $deleteAllConfirmString . '\') '
                . ' && checkoutObj.updateItems({\'empty_customer_cart\': 1})',
            'class' => 'clear'
        ));

        $this->addChild('addto_cart_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Add Selected Product(s) to Shopping Cart'),
            'onclick' => 'checkoutObj.addToCart()',
            'class' => 'add button-to-cart'
        ));

        $this->addChild('cancel_add_products_button', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Cancel'),
            'onclick' => 'checkoutObj.cancelSearch()',
            'class' => 'cancel'
        ));

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
     * Return current customer from regisrty
     *
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->_coreRegistry->registry('checkout_current_customer');
    }

    /**
     * Return current store from regisrty
     *
     * @return \Magento\Core\Model\Store
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
            return $this->getUrl('customer/index/edit', array('id' => $this->_getCustomer()->getId()));
        } else {
            return $this->getUrl('customer/index');
        }
    }

    /**
     * Return URL to controller action
     *
     * @return string
     */
    public function getActionUrl($action)
    {
        return $this->getUrl('checkout/*/' . $action, array('_current' => true));
    }

    /**
     * Return URL to admin order creation
     *
     * @return string
     */
    public function getCreateOrderUrl()
    {
        return $this->getUrl('checkout/*/createOrder', array('_current' => true));
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

    public function getOrderDataJson()
    {
        $actionUrls = array(
            'cart' => $this->getActionUrl('cart'),
            'applyCoupon' => $this->getActionUrl('applyCoupon'),
            'coupon' => $this->getActionUrl('coupon')
        );

        $messages = array(
            'chooseProducts' => __('Choose  products to add to shopping cart.')
        );

        $data = array(
            'action_urls' => $actionUrls,
            'messages' => $messages,
            'customer_id' => $this->_getCustomer()->getId(),
            'store_id' => $this->_getStore()->getId()
        );

        return $this->_jsonEncoder->encode($data);
    }

    /**
     * Retrieve curency name by code
     *
     * @param   string $code
     * @return  string
     */
    public function getCurrencySymbol($code)
    {
        $currency = $this->_locale->currency($code);
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
