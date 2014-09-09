<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Index;

class Index extends \Magento\Invitation\Controller\Index
{
    /**
     * View invitation list in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        if ($block = $this->_view->getLayout()->getBlock('invitations_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->setTitle(__('My Invitations'));
        $this->_view->renderLayout();
    }
}
