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
        $this->_title->add(__('Site Map'));
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
