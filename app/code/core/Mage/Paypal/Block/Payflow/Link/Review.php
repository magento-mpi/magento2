<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal PayflowLink Express Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Payflow_Link_Review extends Mage_Paypal_Block_Express_Review
{

    /**
     * Retrieve payment method and assign additional template values
     *
     * @return Mage_Paypal_Block_Express_Review
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $this->setPlaceOrderUrl($this->getUrl("*/*/placeOrder"));
        $this->setEditUrl();
        $this->setSuccessUrl($this->getUrl("checkout/onepage/success"));
        $this->setUseAjax(true);
        return $this;
    }
}
