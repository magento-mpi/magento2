<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order shipment tracks collection
 *
 */
namespace Magento\Shipping\Model\Resource\Order\Track;

class Collection extends \Magento\Sales\Model\Resource\Order\Shipment\Track\Collection
{
    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Shipping\Model\Order\Track', 'Magento\Sales\Model\Resource\Order\Shipment\Track');
    }
}
