<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RSS Controller for Catalog feeds in Admin
 */
namespace Magento\Rss\Controller\Adminhtml;

class Catalog extends \Magento\Rss\Controller\Adminhtml\Authenticate
{
    /**
     * Notify stock action
     */
    public function notifystockAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Review action
     */
    public function reviewAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
