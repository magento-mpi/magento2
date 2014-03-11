<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Resource\Order\Track;

/**
 * Flat sales order shipment tracks collection
 *
 */
class Collection extends \Magento\Sales\Model\Resource\Order\Shipment\Track\Collection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Shipping\Model\Order\Track', 'Magento\Sales\Model\Resource\Order\Shipment\Track');
    }
}
