<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment _getResource()
 * @method Mage_Sales_Model_Resource_Order_Shipment_Comment getResource()
 * @method int getParentId()
 * @method Mage_Sales_Model_Order_Shipment_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Mage_Sales_Model_Order_Shipment_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Mage_Sales_Model_Order_Shipment_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Mage_Sales_Model_Order_Shipment_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Mage_Sales_Model_Order_Shipment_Comment setCreatedAt(string $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Shipment_Comment extends Mage_Sales_Model_Abstract
{
    /**
     * Shipment instance
     *
     * @var Mage_Sales_Model_Order_Shipment
     */
    protected $_shipment;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Resource_Order_Shipment_Comment');
    }

    /**
     * Declare Shipment instance
     *
     * @param   Mage_Sales_Model_Order_Shipment $shipment
     * @return  Mage_Sales_Model_Order_Shipment_Comment
     */
    public function setShipment(Mage_Sales_Model_Order_Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return $this->_shipment;
    }

    /**
     * Get store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getShipment()) {
            return $this->getShipment()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Before object save
     *
     * @return Mage_Sales_Model_Order_Shipment_Comment
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getShipment()) {
            $this->setParentId($this->getShipment()->getId());
        }

        return $this;
    }
}
