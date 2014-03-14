<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\App;

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
     * @param \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel
     * @param \Magento\App\Http\Context $httpContext
     */
    public function __construct(
        \Magento\Catalog\Model\Product\ProductList\Toolbar $toolbarModel,
        \Magento\App\Http\Context $httpContext
    ) {
        $this->toolbarModel = $toolbarModel;
        $this->httpContext = $httpContext;
    }

    /**
     * Before launch plugin
     *
     * @param \Magento\LauncherInterface $subject
     */
    public function beforeLaunch(\Magento\LauncherInterface $subject)
    {
        if ($this->toolbarModel->getDirection()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_SORT_DIRECTION, $this->toolbarModel->getDirection());
        }
        if ($this->toolbarModel->getOrder()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_SORT_ORDER, $this->toolbarModel->getOrder());
        }
        if ($this->toolbarModel->getMode()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_DISPLAY_MODE, $this->toolbarModel->getMode());
        }
        if ($this->toolbarModel->getLimit()) {
            $this->httpContext->setValue(Data::CONTEXT_CATALOG_LIMIT, $this->toolbarModel->getLimit());
        }
    }
}
