<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

class Index extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * View gift registry list in 'My Account' section
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('giftregistry_list');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->setTitle(__('Gift Registry'));
        $this->_view->renderLayout();
    }
}
