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

class NewAction extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var Initialization\StockDataFilter
     */
    protected $stockFilter;

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param Initialization\StockDataFilter $stockFilter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        Initialization\StockDataFilter $stockFilter
    ) {
        $this->stockFilter = $stockFilter;
        parent::__construct($context, $productBuilder);
    }

    /**
     * Create new product page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('set')) {
            $this->_forward('noroute');
            return;
        }
        $this->_title->add(__('Products'));

        $product = $this->productBuilder->build($this->getRequest());

        $productData = $this->getRequest()->getPost('product');
        if ($productData) {
            $stockData = isset($productData['stock_data']) ? $productData['stock_data'] : array();
            $productData['stock_data'] = $this->stockFilter->filter($stockData);
            $product->addData($productData);
        }

        $this->_title->add(__('New Product'));

        $this->_eventManager->dispatch('catalog_product_new_action', array('product' => $product));

        if ($this->getRequest()->getParam('popup')) {
            $this->_view->loadLayout(array(
                'popup',
                strtolower($this->_request->getFullActionName()),
                'catalog_product_' . $product->getTypeId()
            ));
        } else {
            $this->_view->loadLayout(
                array(
                    'default',
                    strtolower($this->_request->getFullActionName()),
                    'catalog_product_' . $product->getTypeId()
                )
            );
            $this->_setActiveMenu('Magento_Catalog::catalog_products');
        }

        $block = $this->_view->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($product->getStoreId());
        }

        $this->_view->renderLayout();
    }
}
