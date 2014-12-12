<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class NewAction extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Create new customer segment
     *
     * @return void
     */
    public function execute()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
}
