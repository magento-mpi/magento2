<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class CustomOptions extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        parent::__construct($context, $productBuilder);
    }

    /**
     * Show custom options in JSON format for specified products
     *
     * @return void
     */
    public function execute()
    {
        $this->registry->register('import_option_products', $this->getRequest()->getPost('products'));
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
