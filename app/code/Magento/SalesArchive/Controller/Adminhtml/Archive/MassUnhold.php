<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassUnhold extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Unhold orders mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('massUnhold', 'order', null, ['origin' => 'archive']);
    }
}
