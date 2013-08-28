<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Magento_Sales_Model_Resource_Quote_Address_Rate _getResource()
 * @method Magento_Sales_Model_Resource_Quote_Address_Rate getResource()
 * @method int getAddressId()
 * @method Magento_Sales_Model_Quote_Address_Rate setAddressId(int $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Quote_Address_Rate setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Quote_Address_Rate setUpdatedAt(string $value)
 * @method string getCarrier()
 * @method Magento_Sales_Model_Quote_Address_Rate setCarrier(string $value)
 * @method string getCarrierTitle()
 * @method Magento_Sales_Model_Quote_Address_Rate setCarrierTitle(string $value)
 * @method string getCode()
 * @method Magento_Sales_Model_Quote_Address_Rate setCode(string $value)
 * @method string getMethod()
 * @method Magento_Sales_Model_Quote_Address_Rate setMethod(string $value)
 * @method string getMethodDescription()
 * @method Magento_Sales_Model_Quote_Address_Rate setMethodDescription(string $value)
 * @method float getPrice()
 * @method Magento_Sales_Model_Quote_Address_Rate setPrice(float $value)
 * @method string getErrorMessage()
 * @method Magento_Sales_Model_Quote_Address_Rate setErrorMessage(string $value)
 * @method string getMethodTitle()
 * @method Magento_Sales_Model_Quote_Address_Rate setMethodTitle(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Quote_Address_Rate extends Magento_Shipping_Model_Rate_Abstract
{
    /**
     * @var Magento_Sales_Model_Quote_Address
     */
    protected $_address;

    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Quote_Address_Rate');
    }

    /**
     * @return $this|Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getAddress()) {
            $this->setAddressId($this->getAddress()->getId());
        }
        return $this;
    }

    /**
     * @param Magento_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function setAddress(Magento_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }

    /**
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }

    /**
     * @param Magento_Shipping_Model_Rate_Result_Abstract $rate
     * @return $this
     */
    public function importShippingRate(Magento_Shipping_Model_Rate_Result_Abstract $rate)
    {
        if ($rate instanceof Magento_Shipping_Model_Rate_Result_Error) {
            $this->setCode($rate->getCarrier() . '_error')
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setErrorMessage($rate->getErrorMessage());
        } elseif ($rate instanceof Magento_Shipping_Model_Rate_Result_Method) {
            $this->setCode($rate->getCarrier() . '_' . $rate->getMethod())
                ->setCarrier($rate->getCarrier())
                ->setCarrierTitle($rate->getCarrierTitle())
                ->setMethod($rate->getMethod())
                ->setMethodTitle($rate->getMethodTitle())
                ->setMethodDescription($rate->getMethodDescription())
                ->setPrice($rate->getPrice());
        }
        return $this;
    }
}
