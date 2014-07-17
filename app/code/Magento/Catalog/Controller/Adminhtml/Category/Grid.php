<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

class Grid extends \Magento\Catalog\Controller\Adminhtml\Category
{
    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function execute()
    {
        $category = $this->_initCategory(true);
        if (!$category) {
            return;
        }
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Category\Tab\Product',
                'category.product.grid'
            )->toHtml()
        );
    }
}
