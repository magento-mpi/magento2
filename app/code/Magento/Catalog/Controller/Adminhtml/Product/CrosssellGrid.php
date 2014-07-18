<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class CrosssellGrid extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Get crosssell products grid
     *
     * @return void
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.crosssell')
            ->setProductsRelated($this->getRequest()->getPost('products_crosssell', null));
        $this->_view->renderLayout();
    }
}
