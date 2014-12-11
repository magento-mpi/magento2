<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Index extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * List of gift wrappings
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
