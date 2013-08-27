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
 * @method Magento_Sales_Model_Resource_Order_Shipment_Track _getResource()
 * @method Magento_Sales_Model_Resource_Order_Shipment_Track getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Shipment_Track setParentId(int $value)
 * @method float getWeight()
 * @method Magento_Sales_Model_Order_Shipment_Track setWeight(float $value)
 * @method float getQty()
 * @method Magento_Sales_Model_Order_Shipment_Track setQty(float $value)
 * @method int getOrderId()
 * @method Magento_Sales_Model_Order_Shipment_Track setOrderId(int $value)
 * @method string getDescription()
 * @method Magento_Sales_Model_Order_Shipment_Track setDescription(string $value)
 * @method string getTitle()
 * @method Magento_Sales_Model_Order_Shipment_Track setTitle(string $value)
 * @method string getCarrierCode()
 * @method Magento_Sales_Model_Order_Shipment_Track setCarrierCode(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Shipment_Track setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Order_Shipment_Track setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Shipment_Track extends Magento_Sales_Model_Abstract
{
    /**
     * Code of custom carrier
     */
    const CUSTOM_CARRIER_CODE = 'custom';

    protected $_shipment = null;

    protected $_eventPrefix = 'sales_order_shipment_track';
    protected $_eventObject = 'track';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Shipment_Track');
    }

    /**
     * Tracking number getter
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getData('track_number');
    }

    /**
     * Tracking number setter
     *
     * @param string $number
     * @return Magento_Object
     */
    public function setNumber($number)
    {
        return $this->setData('track_number', $number);
    }

    /**
     * Declare Shipment instance
     *
     * @param   Magento_Sales_Model_Order_Shipment $shipment
     * @return  Magento_Sales_Model_Order_Shipment_Item
     */
    public function setShipment(Magento_Sales_Model_Order_Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        if (!($this->_shipment instanceof Magento_Sales_Model_Order_Shipment)) {
            $this->_shipment = Mage::getModel('Magento_Sales_Model_Order_Shipment')->load($this->getParentId());
        }

        return $this->_shipment;
    }

    /**
     * Check whether custom carrier was used for this track
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getCarrierCode() == self::CUSTOM_CARRIER_CODE;
    }

    /**
     * Retrieve hash code of current order
     *
     * @return string
     */
    public function getProtectCode()
    {
        return (string)$this->getShipment()->getProtectCode();
    }

    /**
     * Retrieve detail for shipment track
     *
     * @return string
     */
    public function getNumberDetail()
    {
        $carrierInstance = Mage::getSingleton('Magento_Shipping_Model_Config')
            ->getCarrierInstance($this->getCarrierCode());
        if (!$carrierInstance) {
            $custom = array();
            $custom['title'] = $this->getTitle();
            $custom['number'] = $this->getTrackNumber();
            return $custom;
        } else {
            $carrierInstance->setStore($this->getStore());
        }

        if (!$trackingInfo = $carrierInstance->getTrackingInfo($this->getNumber())) {
            return __('No detail for number "%1"', $this->getNumber());
        }

        return $trackingInfo;
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getShipment()) {
            return $this->getShipment()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Shipment_Track
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }

        return $this;
    }

    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $data
     * @return Magento_Sales_Model_Order_Shipment_Track
     */
    public function addData(array $data)
    {
        if (array_key_exists('number', $data)) {
            $this->setNumber($data['number']);
            unset($data['number']);
        }
        return parent::addData($data);
    }
}
