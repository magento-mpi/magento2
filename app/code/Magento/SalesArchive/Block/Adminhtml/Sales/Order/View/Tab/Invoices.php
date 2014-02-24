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
 * Invoices tab
 */
class Invoices
     extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Invoices
{

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento\SalesArchive\Model\Resource\Order\Invoice\Collection';
    }
}
