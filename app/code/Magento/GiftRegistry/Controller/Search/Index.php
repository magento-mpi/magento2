<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Controller\Search;

class Index extends \Magento\GiftRegistry\Controller\Search
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Gift Registry Search'));
        $this->_view->renderLayout();
    }
}
