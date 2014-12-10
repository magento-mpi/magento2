<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

class MassHold extends \Magento\SalesArchive\Controller\Adminhtml\Archive
{
    /**
     * Hold orders mass action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('massHold', 'order', null, ['origin' => 'archive']);
    }
}
