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
 * Order by SKU Widget Block
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
class Magento_AdvancedCheckout_Block_Widget_Sku
    extends Magento_AdvancedCheckout_Block_Sku_Abstract
    implements Magento_Widget_Block_Interface
{
    /**
     * Retrieve form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('checkout/cart/advancedAdd');
    }
}
