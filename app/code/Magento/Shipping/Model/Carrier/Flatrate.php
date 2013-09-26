<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat rate shipping model
 *
 * @category   Magento
 * @package    Magento_Shipping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Shipping_Model_Carrier_Flatrate
    extends Magento_Shipping_Model_Carrier_Abstract
    implements Magento_Shipping_Model_Carrier_Interface
{
    /**
     * @var string
     */
    protected $_code = 'flatrate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var Magento_Shipping_Model_Rate_ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var Magento_Shipping_Model_Rate_Result_MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory
     * @param Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory,
        Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
    }

    /**
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {

                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeBoxes += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);

        /** @var Magento_Shipping_Model_Rate_Result $result */
        $result = $this->_rateResultFactory->create();
        if ($this->getConfigData('type') == 'O') { // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') { // per item
            $shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
        } else {
            $shippingPrice = false;
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false) {
            /** @var Magento_Shipping_Model_Rate_Result_Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('flatrate');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('flatrate');
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }


            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('flatrate'=>$this->getConfigData('name'));
    }

}
