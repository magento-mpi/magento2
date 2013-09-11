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
 * @method \Magento\Object                                                   getItem()
 * @method \Magento\Catalog\Model\Product                                      getProduct()
 * @method \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Description setItem()
 * @method \Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid\Description setProduct()
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Sku\Errors\Grid;

class Description extends \Magento\Adminhtml\Block\Template
{
    protected $_template = 'sku/errors/grid/description.phtml';

    /**
     * Retrieves HTML code of "Configure" button
     *
     * @return string
     */
    public function getConfigureButtonHtml()
    {
        $canConfigure = $this->getProduct()->canConfigure() && !$this->getItem()->getIsConfigureDisabled();
        $productId = $this->escapeHtml(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($this->getProduct()->getId()));
        $itemSku = $this->escapeHtml(\Mage::helper('Magento\Core\Helper\Data')->jsonEncode($this->getItem()->getSku()));

        /* @var $button \Magento\Adminhtml\Block\Widget\Button */
        $button = $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button', '', array('data' => array(
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
     * @see \Magento\AdvancedCheckout\Helper\Data::ADD_ITEM_STATUS_FAILED_* constants for $code
     *
     * @param \Magento\Object $item
     * @return string
     */
    public function getErrorMessage($item)
    {
        return \Mage::helper('Magento\AdvancedCheckout\Helper\Data')->getMessageByItem($item);
    }
}
