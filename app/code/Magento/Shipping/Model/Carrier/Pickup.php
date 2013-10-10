<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model\Carrier;

class Pickup
    extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var string
     */
    protected $_code = 'pickup';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\Result\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Rate\Result\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory,
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Rate\Result\MethodFactory $rateMethodFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
    }

    /**
     * @param \Magento\Shipping\Model\Rate\Request $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Shipping\Model\Rate\Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        if (!empty($rate)) {
            /** @var \Magento\Shipping\Model\Rate\Result\Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('pickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('store');
            $method->setMethodTitle(__('Store Pickup'));

            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('pickup'=>__('Store Pickup'));
    }

}
