<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Controller\Product;

use \Magento\Review\Model\Review;
use Magento\Catalog\Model\Product as CatalogProduct;

class ListAction extends \Magento\Review\Controller\Product
{
    /**
     * Load specific layout handles by product type id
     *
     * @param CatalogProduct $product
     * @return void
     */
    protected function _initProductLayout($product)
    {
        $this->_view->getPage()->initLayout();
        if ($product->getPageLayout()) {
            /** @var \Magento\Framework\View\Page\Config $pageConfig */
            $pageConfig = $this->_objectManager->get('Magento\Framework\View\Page\Config');
            $pageConfig->setPageLayout($product->getPageLayout());
        }
        $update = $this->_view->getLayout()->getUpdate();
        $this->_view->addPageLayoutHandles(
            array('id' => $product->getId(), 'sku' => $product->getSku(), 'type' => $product->getTypeId())
        );

        $this->_view->loadLayoutUpdates();
        $update->addUpdate($product->getCustomLayoutUpdate());
        $this->_view->generateLayoutXml();
        $this->_view->generateLayoutBlocks();
    }

    /**
     * Show list of product's reviews
     *
     * @return void
     */
    public function execute()
    {
        $product = $this->_initProduct();
        if ($product) {
            $this->_coreRegistry->register('productId', $product->getId());

            $design = $this->_catalogDesign;
            $settings = $design->getDesignSettings($product);
            if ($settings->getCustomDesign()) {
                $design->applyCustomDesign($settings->getCustomDesign());
            }
            $this->_initProductLayout($product);

            // update breadcrumbs
            $breadcrumbsBlock = $this->_view->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'product',
                    array('label' => $product->getName(), 'link' => $product->getProductUrl(), 'readonly' => true)
                );
                $breadcrumbsBlock->addCrumb('reviews', array('label' => __('Product Reviews')));
            }

            $this->_view->renderLayout();
        } elseif (!$this->getResponse()->isRedirect()) {
            $this->_forward('noroute');
        }
    }
}
