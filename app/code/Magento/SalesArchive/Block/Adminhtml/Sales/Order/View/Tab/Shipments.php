<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shipments tab
 *
 */

namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab;

class Shipments
     extends \Magento\Adminhtml\Block\Sales\Order\View\Tab\Shipments
{
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return '\Magento\SalesArchive\Model\Resource\Order\Shipment\Collection';
    }
}
