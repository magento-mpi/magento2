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

class Add
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Sku\AbstractSku
{
    /**
     * Returns JavaScript variable name of AdminCheckout or AdminOrder instance
     *
     * @return string
     */
    public function getJsOrderObject()
    {
        return 'order';
    }

    /**
     * Returns HTML ID of the error grid
     *
     * @return string
     */
    public function getErrorGridId()
    {
        return 'order_errors';
    }

    /**
     * Retrieve file upload URL
     *
     * @return string
     */
    public function getFileUploadUrl()
    {
        return $this->getUrl('*/sales_order_create/processData');
    }

    /**
     * Retrieve context specific JavaScript
     *
     * @return string
     */
    public function getContextSpecificJs()
    {
        return '
            var parentAreasLoaded = ' . $this->getJsOrderObject() . '.areasLoaded;
            ' . $this->getJsOrderObject() . '.areasLoaded = function () {
                initSku();
                parentAreasLoaded();
            };';
    }
}
