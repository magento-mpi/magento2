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
     * Return required ACL resource for current action
     *
     * @return bool|string
     */
    protected function _getActionAclResource()
    {
        $acl = array(
            'notifystock' => 'Magento_Catalog::products',
            'review' => 'Magento_Review::reviews_all'
        );
        $action = $this->getRequest()->getActionName();
        return isset($acl[$action]) ? $acl[$action] : false;
    }

    /**
     * Notify stock action
     */
    public function notifystockAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Review action
     */
    public function reviewAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
