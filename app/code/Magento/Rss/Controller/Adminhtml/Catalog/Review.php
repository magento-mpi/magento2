<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Adminhtml\Catalog;

class Review extends \Magento\Rss\Controller\Adminhtml\Authenticate
{
    /**
     * Review action
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
