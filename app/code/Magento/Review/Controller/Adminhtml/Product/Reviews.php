<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Controller\Adminhtml\Product;

class Reviews extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder
    ) {
        $this->productBuilder = $productBuilder;
         parent::__construct($context);
    }

    /**
     * Get product reviews grid
     *
     * @return void
     */
    public function gridAction()
    {
        $product = $this->productBuilder->build($this->getRequest());
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('admin.product.reviews')
            ->setProductId($product->getId())
            ->setUseAjax(true);
        $this->_view->renderLayout();
    }
} 
