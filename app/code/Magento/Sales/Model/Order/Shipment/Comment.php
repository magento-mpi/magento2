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
 * @method Magento_Sales_Model_Resource_Order_Shipment_Comment _getResource()
 * @method Magento_Sales_Model_Resource_Order_Shipment_Comment getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Shipment_Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method Magento_Sales_Model_Order_Shipment_Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method Magento_Sales_Model_Order_Shipment_Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Magento_Sales_Model_Order_Shipment_Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Shipment_Comment setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Shipment_Comment extends Magento_Sales_Model_Abstract
{
    /**
     * Shipment instance
     *
     * @var Magento_Sales_Model_Order_Shipment
     */
    protected $_shipment;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Shipment_Comment');
    }

    /**
     * Declare Shipment instance
     *
     * @param   Magento_Sales_Model_Order_Shipment $shipment
     * @return  Magento_Sales_Model_Order_Shipment_Comment
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
        return $this->_shipment;
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
     * Before object save
     *
     * @return Magento_Sales_Model_Order_Shipment_Comment
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
