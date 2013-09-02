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
 * Block with description of why item has not been added to ordered items list
 *
 * @method Magento_Object                                                   getItem()
 * @method Magento_Catalog_Model_Product                                      getProduct()
 * @method Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Description setItem()
 * @method Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Description setProduct()
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Description extends Magento_Backend_Block_Template
{
    protected $_template = 'sku/errors/grid/description.phtml';

    /**
     * Checkout data
     *
     * @var Magento_AdvancedCheckout_Helper_Data
     */
    protected $_checkoutData = null;

    /**
     * @param Magento_AdvancedCheckout_Helper_Data $checkoutData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_AdvancedCheckout_Helper_Data $checkoutData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieves HTML code of "Configure" button
     *
     * @return string
     */
    public function getConfigureButtonHtml()
    {
        $canConfigure = $this->getProduct()->canConfigure() && !$this->getItem()->getIsConfigureDisabled();
        $productId = $this->escapeHtml($this->_coreData->jsonEncode($this->getProduct()->getId()));
        $itemSku = $this->escapeHtml($this->_coreData->jsonEncode($this->getItem()->getSku()));

        /* @var $button Magento_Adminhtml_Block_Widget_Button */
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button', '', array('data' => array(
            'class'    => $canConfigure ? 'action-configure' : 'action-configure action-disabled',
            'onclick'  => $canConfigure ? "addBySku.configure({$productId}, {$itemSku})" : '',
            'disabled' => !$canConfigure,
            'label'    => __('Configure'),
            'type'     => 'button',
        )));

        return $button->toHtml();
    }

    /**
     * Retrieve HTML name for element
     *
     * @return string
     */
    public function getSourceId()
    {
        return $this->_prepareLayout()->getLayout()->getBlock('sku_error_grid')->getId();
    }

    /**
     * Returns error message of the item
     * @see Magento_AdvancedCheckout_Helper_Data::ADD_ITEM_STATUS_FAILED_* constants for $code
     *
     * @param Magento_Object $item
     * @return string
     */
    public function getErrorMessage($item)
    {
        return $this->_checkoutData->getMessageByItem($item);
    }
}
