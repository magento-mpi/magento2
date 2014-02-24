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
 * RSS Controller for Orders feed in Admin
 */
class Order extends \Magento\Rss\Controller\Adminhtml\Authenticate
{
    /**
     * New orders action
     *
     * @return void
     */
    public function newAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
