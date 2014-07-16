<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Urlrewrite;

class Index extends \Magento\Backend\Controller\Adminhtml\Urlrewrite
{
    /**
     * Show URL rewrites index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('URL Redirects'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_urlrewrite');
        $this->_view->renderLayout();
    }
}
