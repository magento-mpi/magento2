<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Tab;

/**
 * Shipments tab
 */
class Shipments
     extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Shipments
{
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Shipment\Collection';
    }
}
