<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class Index extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Invitation list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Invitation::customer_magento_invitation');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Invitations'));
        $this->_view->renderLayout();
    }
}
