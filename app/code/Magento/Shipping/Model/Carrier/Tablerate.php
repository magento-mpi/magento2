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

class Tablerate
    extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    /**
     * @var string
     */
    protected $_code = 'tablerate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var string
     */
    protected $_defaultConditionName = 'package_weight';

    /**
     * @var array
     */
    protected $_conditionNames = array();

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Shipping\Model\Rate\Result\MethodFactory
     */
    protected $_resultMethodFactory;

    /**
     * @var \Magento\Shipping\Model\Resource\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory
     * @param \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory
     * @param \Magento\Shipping\Model\Rate\Result\MethodFactory $resultMethodFactory
     * @param \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Shipping\Model\Rate\Result\ErrorFactory $rateErrorFactory,
        \Magento\Core\Model\Log\AdapterFactory $logAdapterFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Shipping\Model\Rate\Result\MethodFactory $resultMethodFactory,
        \Magento\Shipping\Model\Resource\Carrier\TablerateFactory $tablerateFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
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

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        if ($request->getAllItems()) {
            $freePackageValue = 0;
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeShipping = is_numeric($item->getFreeShipping()) ? $item->getFreeShipping() : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                }
            }
            $oldValue = $request->getPackageValue();
            $request->setPackageValue($oldValue - $freePackageValue);
        }

        if (!$request->getConditionName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_defaultConditionName);
        }

         // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $rate = $this->getRate($request);

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        if (!empty($rate) && $rate['price'] >= 0) {
            /** @var \Magento\Shipping\Model\Rate\Result\Method $method */
            $method = $this->_resultMethodFactory->create();

            $method->setCarrier('tablerate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('bestway');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || ($request->getPackageQty() == $freeQty)) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($rate['price']);
            }

            $method->setPrice($shippingPrice);
            $method->setCost($rate['cost']);

            $result->append($method);
        } else {
            /** @var \Magento\Shipping\Model\Rate\Result\Error $error */
            $error = $this->_rateErrorFactory->create(array('data' => array(
                'carrier'       => $this->_code,
                'carrier_title' => $this->getConfigData('title'),
                'error_message' => $this->getConfigData('specificerrmsg'),
            )));
            $result->append($error);
        }

        return $result;
    }

    /**
     * @param \Magento\Shipping\Model\Rate\Request $request
     * @return array|bool
     */
    public function getRate(\Magento\Shipping\Model\Rate\Request $request)
    {
        return $this->_tablerateFactory->create()->getRate($request);
    }

    /**
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Shipping\Exception
     */
    public function getCode($type, $code='')
    {
        $codes = array(

            'condition_name'=>array(
                'package_weight' => __('Weight vs. Destination'),
                'package_value'  => __('Price vs. Destination'),
                'package_qty'    => __('# of Items vs. Destination'),
            ),

            'condition_name_short'=>array(
                'package_weight' => __('Weight (and above)'),
                'package_value'  => __('Order Subtotal (and above)'),
                'package_qty'    => __('# of Items (and above)'),
            ),

        );

        if (!isset($codes[$type])) {
            throw new \Magento\Shipping\Exception( __('Please correct Table Rate code type: %1.', $type));
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new \Magento\Shipping\Exception(__('Please correct Table Rate code for type %1: %2.', $type, $code));
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('bestway'=>$this->getConfigData('name'));
    }

}
