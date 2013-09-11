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
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Track _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Track getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Shipment\Track setParentId(int $value)
 * @method float getWeight()
 * @method \Magento\Sales\Model\Order\Shipment\Track setWeight(float $value)
 * @method float getQty()
 * @method \Magento\Sales\Model\Order\Shipment\Track setQty(float $value)
 * @method int getOrderId()
 * @method \Magento\Sales\Model\Order\Shipment\Track setOrderId(int $value)
 * @method string getDescription()
 * @method \Magento\Sales\Model\Order\Shipment\Track setDescription(string $value)
 * @method string getTitle()
 * @method \Magento\Sales\Model\Order\Shipment\Track setTitle(string $value)
 * @method string getCarrierCode()
 * @method \Magento\Sales\Model\Order\Shipment\Track setCarrierCode(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Shipment\Track setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Order\Shipment\Track setUpdatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Shipment;

class Track extends \Magento\Sales\Model\AbstractModel
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
        $this->_init('\Magento\Sales\Model\Resource\Order\Shipment\Track');
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
     * @return \Magento\Object
     */
    public function setNumber($number)
    {
        return $this->setData('track_number', $number);
    }

    /**
     * Declare Shipment instance
     *
     * @param   \Magento\Sales\Model\Order\Shipment $shipment
     * @return  \Magento\Sales\Model\Order\Shipment\Item
     */
    public function setShipment(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        if (!($this->_shipment instanceof \Magento\Sales\Model\Order\Shipment)) {
            $this->_shipment = \Mage::getModel('Magento\Sales\Model\Order\Shipment')->load($this->getParentId());
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
        $carrierInstance = \Mage::getSingleton('Magento\Shipping\Model\Config')
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
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if ($this->getShipment()) {
            return $this->getShipment()->getStore();
        }
        return \Mage::app()->getStore();
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
     * @return \Magento\Sales\Model\Order\Shipment\Track
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
     * @return \Magento\Sales\Model\Order\Shipment\Track
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
