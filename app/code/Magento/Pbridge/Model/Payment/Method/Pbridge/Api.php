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
namespace Magento\Pbridge\Model\Payment\Method\Pbridge;

class Api extends \Magento\Pbridge\Model\Pbridge\Api\AbstractApi
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
        $request = \Mage::helper('Magento\Pbridge\Helper\Data')->getRequestParams($request);
        $request = array('data' => \Mage::helper('Magento\Pbridge\Helper\Data')->encrypt(json_encode($request)));
        return http_build_query($request, '', '&');
    }

    public function validateToken($orderId)
    {
        \Magento\Profiler::start('pbridge_validate_token', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:validate_token'
        ));
        $this->_call(array(
            'client_identifier' => $orderId,
            'payment_action' => 'validate_token'
        ));
        \Magento\Profiler::stop('pbridge_validate_token');
        return $this;
    }

    /**
     * Authorize
     *
     * @param \Magento\Object $request
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    public function doAuthorize($request)
    {
        \Magento\Profiler::start('pbridge_place', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:place'
        ));
        $request->setData('payment_action', 'place');
        $this->_call($request->getData());
        \Magento\Profiler::stop('pbridge_place');
        return $this;
    }

    /**
     * Capture
     *
     * @param \Magento\Object $request
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    public function doCapture($request)
    {
        \Magento\Profiler::start('pbridge_capture', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:capture'
        ));
        $request->setData('payment_action', 'capture');
        $this->_call($request->getData());
        \Magento\Profiler::stop('pbridge_capture');
        return $this;
    }

    /**
     * Refund
     *
     * @param \Magento\Object $request
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    public function doRefund($request)
    {
        \Magento\Profiler::start('pbridge_refund', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:refund'
        ));
        $request->setData('payment_action', 'refund');
        $this->_call($request->getData());
        \Magento\Profiler::stop('pbridge_refund');
        return $this;
    }

    /**
     * Void
     *
     * @param \Magento\Object $request
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    public function doVoid($request)
    {
        \Magento\Profiler::start('pbridge_void', array(
            'group' => 'pbridge',
            'operation' => 'pbridge:void'
        ));
        $request->setData('payment_action', 'void');
        $this->_call($request->getData());
        \Magento\Profiler::stop('pbridge_void');
        return $this;
    }
}
