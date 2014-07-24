<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class Categories extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Get categories fieldset block
     *
     * @return void
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
