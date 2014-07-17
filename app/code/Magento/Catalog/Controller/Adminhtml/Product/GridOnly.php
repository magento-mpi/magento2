<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class GridOnly extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Get specified tab grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Products'));

        $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();

        $block = $this->getRequest()->getParam('gridOnlyBlock');
        $blockClassSuffix = str_replace(' ', '_', ucwords(str_replace('_', ' ', $block)));

        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\\' . $blockClassSuffix
            )->toHtml()
        );
    }
}
