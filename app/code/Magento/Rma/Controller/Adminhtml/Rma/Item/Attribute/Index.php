<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute;

class Index extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Returns Attributes'));
        $this->_view->renderLayout();
    }
}
