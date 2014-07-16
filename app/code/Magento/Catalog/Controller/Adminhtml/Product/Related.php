<?php
/**
 * Get related products grid and serializer block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Related extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('catalog.product.edit.tab.related')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->_view->renderLayout();
    }
}
