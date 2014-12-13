<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class Error extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Empty page for final errors occurred
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_initTitle();
        $this->_view->renderLayout();
    }
}
