<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge API model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Payment_Method_Pbridge_Api extends Magento_Pbridge_Model_Pbridge_Api_Abstract
{
    /**
     * Prepare, merge, encrypt required params for Payment Bridge and payment request params.
     * Return request params as http query string
     *
     * @param array $request
     * @return string
     */
    protected function _prepareRequestParams($request)
    {
        $request['action'] = 'Payments';
        $request['token'] = $this->getMethodInstance()->getPbridgeResponse('token');
        $request = $this->_pbridgeData->getRequestParams($request);
        $request = array('data' => $this->_pbridgeData->encrypt(json_encode($request)));
        return http_build_query($request, '', '&');
    }

    public function validateToken($orderId)
    {
        Magento_Profiler::start('pbridge_validate_token', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:validate_token'
        ));
        $this->_call(array(
            'client_identifier' => $orderId,
            'payment_action' => 'validate_token'
        ));
        Magento_Profiler::stop('pbridge_validate_token');
        return $this;
    }

    /**
     * Authorize
     *
     * @param Magento_Object $request
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doAuthorize($request)
    {
        Magento_Profiler::start('pbridge_place', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:place'
        ));
        $request->setData('payment_action', 'place');
        $this->_call($request->getData());
        Magento_Profiler::stop('pbridge_place');
        return $this;
    }

    /**
     * Capture
     *
     * @param Magento_Object $request
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doCapture($request)
    {
        Magento_Profiler::start('pbridge_capture', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:capture'
        ));
        $request->setData('payment_action', 'capture');
        $this->_call($request->getData());
        Magento_Profiler::stop('pbridge_capture');
        return $this;
    }

    /**
     * Refund
     *
     * @param Magento_Object $request
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doRefund($request)
    {
        Magento_Profiler::start('pbridge_refund', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:refund'
        ));
        $request->setData('payment_action', 'refund');
        $this->_call($request->getData());
        Magento_Profiler::stop('pbridge_refund');
        return $this;
    }

    /**
     * Void
     *
     * @param Magento_Object $request
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doVoid($request)
    {
        Magento_Profiler::start('pbridge_void', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:void'
        ));
        $request->setData('payment_action', 'void');
        $this->_call($request->getData());
        Magento_Profiler::stop('pbridge_void');
        return $this;
    }
}
