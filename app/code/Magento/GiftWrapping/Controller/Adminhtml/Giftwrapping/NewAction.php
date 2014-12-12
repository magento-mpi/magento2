<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class NewAction extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Create new gift wrapping
     *
     * @return void
     */
    public function execute()
    {
        $this->_initModel();
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Gift Wrapping'));
        $this->_view->renderLayout();
    }
}
