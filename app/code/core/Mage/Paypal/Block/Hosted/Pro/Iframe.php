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
 * Hosted Pro iframe block
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_Block_Hosted_Pro_Iframe extends Mage_Paypal_Block_Iframe
{
    /**
     * Internal constructor
     * Set payment method code
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_HOSTEDPRO;
    }

    /**
     * Get iframe action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_form_url');
    }
}
