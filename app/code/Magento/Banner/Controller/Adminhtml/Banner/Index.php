<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Controller\Adminhtml\Banner;

class Index extends \Magento\Banner\Controller\Adminhtml\Banner
{
    /**
     * Banners list
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Banner::cms_magento_banner');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Banners'));
        $this->_view->renderLayout();
    }
}
