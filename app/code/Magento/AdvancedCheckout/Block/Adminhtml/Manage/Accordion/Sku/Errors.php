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
 * Add by SKU errors accordion
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Sku;

class Errors
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\AbstractErrors
{
    /**
     * @var \Magento\Registry
     */
    protected $_registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdvancedCheckout\Model\CartFactory $cartFactory
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdvancedCheckout\Model\CartFactory $cartFactory,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $cartFactory, $data);
    }

    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        $customer = $this->_registry->registry('checkout_current_customer');
        $store = $this->_registry->registry('checkout_current_store');
        $params = array(
            'customer'   => $customer->getId(),
            'store'    => $store->getId()
        );
        return $this->getUrl('checkout/index/configureProductToAdd', $params);
    }

    /**
     * Retrieve additional JavaScript for error grid
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        return "addBySku.addErrorSourceGrid({htmlId: '{$this->getId()}', listType: '{$this->getListType()}'})";
    }

    /**
     * Returns current store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        return $this->_registry->registry('checkout_current_store');
    }

    /**
     * Get title of button, that adds products to shopping cart
     *
     * @return string
     */
    public function getAddButtonTitle()
    {
        return __('Add to Shopping Cart');
    }
}
