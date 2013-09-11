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
 * Customer Order By SKU block
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class Sku extends \Magento\AdvancedCheckout\Block\Sku\AbstractSku
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/*/uploadFile');
    }

    /**
     * Check whether form should be multipart
     *
     * @return bool
     */
    public function getIsMultipart()
    {
        return true;
    }
}
