<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Controller\Adminhtml\Sitemap;

use \Magento\Backend\App\Action;

class Index extends \Magento\Sitemap\Controller\Adminhtml\Sitemap
{
    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Site Map'));
        $this->_view->renderLayout();
    }
}
