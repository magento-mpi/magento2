<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class ChooserGrid extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Grid ajax action in chooser mode
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
