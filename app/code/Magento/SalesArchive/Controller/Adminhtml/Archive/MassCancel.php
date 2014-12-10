<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
