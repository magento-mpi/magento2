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
