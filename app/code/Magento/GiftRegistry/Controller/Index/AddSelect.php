<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

class AddSelect extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Add select gift registry action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $block = $this->_view->getLayout()->getBlock('giftregistry_addselect');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->pageConfig->setTitle(__('Create Gift Registry'));
        $this->_view->renderLayout();
    }
}
