<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Index extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Product list page
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Products'));
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Catalog::catalog_products');
        $this->_view->renderLayout();
    }
}
