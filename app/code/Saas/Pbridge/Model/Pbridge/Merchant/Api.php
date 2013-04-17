<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Pbridge_Model_Pbridge_Merchant_Api extends Enterprise_Pbridge_Model_Pbridge_Api_Abstract
{
    /**
     *
     * @var string
     */
    protected $_action   = null;

    /**
     * Prepare, merge, encrypt required params for Payment Bridge and payment request params.
     * Return request params as http query string
     *
     * @param array $request
     * @return string
     */
    protected function _prepareRequestParams($request)
    {
        if (!$this->_action) {
            Mage::throwException(Mage::helper('Saas_Pbridge_Helper_Data')->__('Payment Bridge action is not set.'));
        }
        $request['action'] = $this->_action;
        return parent::_prepareRequestParams($request);
    }

    /**
     * Update merchant payments configuration
     *
     * @param array $request
     * @return Saas_Pbridge_Model_Pbridge_Merchant_Api
     */
    public function doUpdatePaymentsConfiguration($request)
    {
        $this->_action = 'UpdatePaymentsCfg';
        $this->doUpdateConfiguration($request);
        return $this;
    }

    /**
     * Update merchant pyament services configuration
     *
     * @param array $request
     * @return Saas_Pbridge_Model_Pbridge_Merchant_Api
     */
    public function doUpdatePaymentServicesConfiguration($request)
    {
        $this->_action = 'UpdatePaymentServicesCfg';
        $this->doUpdateConfiguration($request);
        return $this;
    }

    /**
     * Update merchant configuration
     *
     * @param array $request
     * @return Saas_Pbridge_Model_Pbridge_Merchant_Api
     */
    public function doUpdateConfiguration($request)
    {
        $this->_call($request);
        return $this;
    }
}
