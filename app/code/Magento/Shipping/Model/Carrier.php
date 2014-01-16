<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model;

class Carrier extends \Magento\Object
{
    /**
     * @var \Magento\Sales\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var \Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    protected $_carrier = null;

    /**
     * @param \Magento\Sales\Model\CarrierFactory $carrierFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\CarrierFactory $carrierFactory,
        array $data = array()
    ) {
        $this->_carrierFactory = $carrierFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool|float|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function getShippingCarrier(\Magento\Sales\Model\Order $order)
    {
        if (!$this->_carrier) {
            $carrierModel = false;
            /**
             * $method - carrier_method
             */
            $method = $order->getShippingMethod(true);
            if ($method instanceof \Magento\Object) {
                $carrierModel = $this->_carrierFactory->create($method->getCarrierCode());
            }
            $this->_carrier = $carrierModel;
        }
        return $this->_carrier;
    }
}
