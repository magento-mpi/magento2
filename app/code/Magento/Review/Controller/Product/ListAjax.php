<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Product;

class ListAjax extends \Magento\Review\Controller\Product
{
    /**
     * Show list of product's reviews
     *
     * @return void
     */
    public function execute()
    {
        $this->_initProduct();
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
