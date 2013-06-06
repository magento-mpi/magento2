<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Paypal_Model_Api_Nvp extends Mage_Paypal_Model_Api_Nvp
{
    /**
     * Internal constructor
     * Change global map SUBJECT code
     */
    protected function _construct()
    {
        $this->_globalMap['SUBJECT'] = 'boarding_account';
        array_push($this->_eachCallRequest, 'SUBJECT');
    }

    /**
     * PayPal tenant merchant email getter
     *
     * @return string
     */
    public function getBoardingAccount()
    {
        if ($this->_getDataOrConfig('receiver_id')) {
            return $this->_getDataOrConfig('receiver_id');
        }
        return $this->_getDataOrConfig('boarding_account');
    }

    /**
     * Do not remove SUBJECT field from request
     *
     * @param &array $requestFields
     */
    protected function _prepareExpressCheckoutCallRequest(&$requestFields)
    {
        //nothing to do
    }
}
