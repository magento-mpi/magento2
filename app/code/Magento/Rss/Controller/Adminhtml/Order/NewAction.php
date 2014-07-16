<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Adminhtml\Order;

class NewAction extends \Magento\Rss\Controller\Adminhtml\Authenticate
{
    /**
     * New orders action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
