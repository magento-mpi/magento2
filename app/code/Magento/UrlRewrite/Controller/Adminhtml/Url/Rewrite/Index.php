<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;

class Index extends \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite
{
    /**
     * Show URL rewrites index page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('URL Rewrites'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_UrlRewrite::urlrewrite');
        $this->_view->renderLayout();
    }
}
