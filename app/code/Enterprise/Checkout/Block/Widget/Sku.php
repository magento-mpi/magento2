<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Order by SKU Widget Block
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Block_Widget_Sku
    extends Enterprise_Checkout_Block_Sku_Abstract
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
