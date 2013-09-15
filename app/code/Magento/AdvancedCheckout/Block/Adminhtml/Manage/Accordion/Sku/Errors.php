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
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Returns url to configure item
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        $customer = $this->_coreRegistry->registry('checkout_current_customer');
        $store = $this->_coreRegistry->registry('checkout_current_store');
        $params = array(
            'customer'   => $customer->getId(),
            'store'    => $store->getId()
        );
        return $this->getUrl('*/checkout/configureProductToAdd', $params);
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
        return $this->_coreRegistry->registry('checkout_current_store');
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
