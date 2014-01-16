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
 * Form for adding products by SKU
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sales\Order\Create\Sku;

class Errors
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors
{
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdvancedCheckout\Model\CartFactory $cartFactory
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdvancedCheckout\Model\CartFactory $cartFactory,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($context, $cartFactory, $data);
    }

    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl('sales/order_create/configureProductToAdd');
    }

    /**
     * Returns enterprise cart model with custom session for order create page
     *
     * @return \Magento\AdvancedCheckout\Model\Cart
     */
    public function getCart()
    {
        if (!$this->_cart) {
            $this->_cart = parent::getCart()->setSession($this->_sessionQuote);
        }
        return $this->_cart;
    }

    /**
     * Returns current store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        $storeId = $this->getCart()->getSession()->getStoreId();
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get title of button, that adds products to order
     *
     * @return string
     */
    public function getAddButtonTitle()
    {
        return __('Add Products to Order');
    }
}
