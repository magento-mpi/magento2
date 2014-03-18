<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\App\Action;

use Magento\Catalog\Helper\Data;

/**
 * Class ContextPlugin
 */
class ContextPlugin
{
    /**
     * @var \Magento\Catalog\Model\Product\ProductList\Toolbar
     */
    protected $toolbarModel;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Catalog\Helper\Product\ProductList
     */
    protected $productListHelper;
    
    /**
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Product\ProductList $productListHelper
     */
    public function __construct(
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Product\ProductList $productListHelper
    ) {
        $this->toolbarModel = $toolbarModel;
        $this->httpContext = $httpContext;
        $this->productListHelper = $productListHelper;
    }

    /**
     * @param \Magento\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     * @return mixed
     */
    public function aroundDispatch(
        \Magento\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $this->httpContext->setValue(
            Data::CONTEXT_CATALOG_SORT_DIRECTION,
            $this->toolbarModel->getDirection(),
            \Magento\Catalog\Helper\Product\ProductList::DEFAULT_SORT_DIRECTION
        );
        $this->httpContext->setValue(
            Data::CONTEXT_CATALOG_SORT_ORDER,
            $this->toolbarModel->getOrder(),
            $this->productListHelper->getDefaultSortField()
        );
        $this->httpContext->setValue(
            Data::CONTEXT_CATALOG_DISPLAY_MODE,
            $this->toolbarModel->getMode(),
            $this->productListHelper->getDefaultViewMode()
        );
        $this->httpContext->setValue(
            Data::CONTEXT_CATALOG_LIMIT,
            $this->toolbarModel->getLimit(),
            $this->productListHelper->getDefaultLimitPerPageValue($this->productListHelper->getDefaultViewMode())
        );
        return $proceed($request);
    }
}
