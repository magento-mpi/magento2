<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal PayflowLink Express Onepage checkout block
 *
 * @deprecated since 1.6.2.0
 * @category   Magento
 * @package    Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Payflow_Link_Review extends Magento_Paypal_Block_Express_Review
{

    /**
     * Retrieve payment method and assign additional template values
     *
     * @return Magento_Paypal_Block_Express_Review
     */
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }
}
