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
    protected function _getActionAclResource()
    {
        $acl = array(
            'notifystock' => 'Magento_Catalog::products',
            'review' => 'Magento_Review::reviews_all'
        );
        $action = $this->getRequest()->getActionName();
        return isset($acl[$action]) ? $acl[$action] : false;
    }

    public function notifystockAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function reviewAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
