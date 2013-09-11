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
 * Flat sales order shipment tracks collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Shipment\Track;

class Collection
    extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_track_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_shipment_track_collection';

    /**
     * Order field
     *
     * @var string
     */
    protected $_orderField     = 'order_id';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Shipment\Track', 'Magento\Sales\Model\Resource\Order\Shipment\Track');
    }

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return \Magento\Sales\Model\Resource\Order\Shipment\Track\Collection
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addFieldToFilter('parent_id', $shipmentId);
        return $this;
    }
}
