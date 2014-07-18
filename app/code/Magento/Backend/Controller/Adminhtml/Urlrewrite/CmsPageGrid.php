<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Urlrewrite;

class CmsPageGrid extends \Magento\Backend\Controller\Adminhtml\Urlrewrite
{
    /**
     * Ajax CMS pages grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock('Magento\Backend\Block\Urlrewrite\Cms\Page\Grid')->toHtml()
        );
    }
}
