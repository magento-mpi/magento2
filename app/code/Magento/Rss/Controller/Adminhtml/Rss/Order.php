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
 * RSS Controller for Orders feed in Admin
 */
namespace Magento\Rss\Controller\Adminhtml\Rss;

class Order extends \Magento\Rss\Controller\Adminhtml\Rss\Authenticate
{
    protected function _getActionAclResource()
    {
        return 'Magento_Sales::sales_order';
    }

    public function newAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
