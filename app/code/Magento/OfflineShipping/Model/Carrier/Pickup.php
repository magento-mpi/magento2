<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\OfflineShipping\Model\Carrier;

class Pickup extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
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
     * @var \Magento\Sales\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Sales\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Sales\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Sales\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Sales\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
    }

    /**
     * @param \Magento\Sales\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Sales\Model\Quote\Address\RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        if (!empty($rate)) {
            /** @var \Magento\Sales\Model\Quote\Address\RateResult\Method $method */
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
        return array('pickup' => __('Store Pickup'));
    }
}
