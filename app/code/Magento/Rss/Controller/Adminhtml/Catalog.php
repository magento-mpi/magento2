<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rss\Controller\Adminhtml;

/**
 * RSS Controller for Catalog feeds in Admin
 */
class Catalog extends \Magento\Rss\Controller\Adminhtml\Authenticate
{
    /**
     * Notify stock action
     *
     * @return void
     */
    public function notifystockAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Review action
     *
     * @return void
     */
    public function reviewAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
