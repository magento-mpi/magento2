<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Product extends Action
{
    /**
     * @var \Magento\Catalog\Controller\Adminhtml\Product\Builder
     */
    protected $productBuilder;

    /**
     * @param Action\Context $context
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
     * Add "super" attribute from popup window
     */
    public function addAttributeAction()
    {
        $this->_view->loadLayout('popup');
        $this->productBuilder->build($this->getRequest());
        $attributeBlock = $this->_view->getLayout()
            ->createBlock('Magento\ConfigurableProduct\Block\Adminhtml\Product\Attribute\NewAttribute\Product\Created');
        $this->_addContent($attributeBlock);
        $this->_view->renderLayout();
    }
}
