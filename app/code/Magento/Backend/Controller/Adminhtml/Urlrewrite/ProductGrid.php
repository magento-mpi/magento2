<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\Urlrewrite;

class ProductGrid extends \Magento\Backend\Controller\Adminhtml\Urlrewrite
{
    /**
     * Ajax products grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock('Magento\Backend\Block\Urlrewrite\Catalog\Product\Grid')->toHtml()
        );
    }
}
