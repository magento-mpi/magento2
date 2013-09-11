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
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Comment _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Comment getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Shipment\Comment setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method \Magento\Sales\Model\Order\Shipment\Comment setIsCustomerNotified(int $value)
 * @method int getIsVisibleOnFront()
 * @method \Magento\Sales\Model\Order\Shipment\Comment setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method \Magento\Sales\Model\Order\Shipment\Comment setComment(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Shipment\Comment setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Shipment;

class Comment extends \Magento\Sales\Model\AbstractModel
{
    /**
     * Shipment instance
     *
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $_shipment;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Resource\Order\Shipment\Comment');
    }

    /**
     * Declare Shipment instance
     *
     * @param   \Magento\Sales\Model\Order\Shipment $shipment
     * @return  \Magento\Sales\Model\Order\Shipment\Comment
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
        return $this->_shipment;
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
     * Before object save
     *
     * @return \Magento\Sales\Model\Order\Shipment\Comment
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
