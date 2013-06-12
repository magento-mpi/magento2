<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Paypal Express review checkout block for permissions
 */
class Saas_Paypal_Block_Boarding_Express_Review extends Mage_Paypal_Block_Express_Review
{
    /**
     * Replace 'Place Order' link
     *
     * @return Saas_Paypal_Block_Boarding_Express_Review
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $this->setPlaceOrderUrl($this->getUrl("{$this->_paypalActionPrefix}/boarding_express/placeOrder"));
        return $this;
    }

}
