<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassCancel extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Cancel orders mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('massCancel', 'order', null, ['origin' => 'archive']);
    }
}
