<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order\Shipment;

/**
 * Flat sales order shipment comment resource
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Comment extends \Magento\Sales\Model\Resource\Order\AbstractOrder
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_shipment_comment_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_flat_shipment_comment', 'entity_id');
    }
}
