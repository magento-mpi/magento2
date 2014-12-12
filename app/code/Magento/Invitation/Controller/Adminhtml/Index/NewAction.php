<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class NewAction extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Create new invitation form
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
        $this->_view->renderLayout();
    }
}
