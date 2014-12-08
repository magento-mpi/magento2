<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassPrintShippingLabel extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Print shipping labels mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('massPrintShippingLabel', 'order_shipment', 'admin', ['origin' => 'archive']);
    }
}
