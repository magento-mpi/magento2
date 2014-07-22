<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\CatalogUrlRewrite\Helper\Data as CatalogUrlRewriteHelper;
use Magento\CatalogUrlRewrite\Service\V1\CategoryUrlGeneratorInterface;
use Magento\CatalogUrlRewrite\Service\V1\ProductUrlGeneratorInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\UrlSaveInterface;

class Observer
{
    /**
     * @var CategoryUrlGeneratorInterface
     */
    protected $categoryUrlGenerator;

    /**
     * @var CategoryUrlGeneratorInterface
     */
    protected $productUrlGenerator;

    /**
     * @var UrlSaveInterface
     */
    protected $urlSave;

    /**
     * @var CatalogUrlRewriteHelper
     */
    protected $catalogUrlRewriteHelper;

    /**
     * @param CategoryUrlGeneratorInterface $categoryUrlGenerator
     * @param ProductUrlGeneratorInterface $productUrlGenerator
     * @param UrlSaveInterface $urlSave
     * @param CatalogUrlRewriteHelper $catalogUrlRewriteHelper
     */
    public function __construct(
        CategoryUrlGeneratorInterface $categoryUrlGenerator,
        ProductUrlGeneratorInterface $productUrlGenerator,
        UrlSaveInterface $urlSave,
        CatalogUrlRewriteHelper $catalogUrlRewriteHelper
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlSave = $urlSave;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteSaving(EventObserver $observer)
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $observer->getEvent()->getCategory();

        if (!$this->catalogUrlRewriteHelper->isRootCategory($category)
            && (!$category->getData('url_key') || $category->getOrigData('url_key') != $category->getData('url_key'))
        ) {
            // TODO: fix service parameter (@TODO: UrlRewrite)
            $this->urlSave->save($this->categoryUrlGenerator->generate($category));

            $products = $category->getProductCollection()
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('url_path');

            foreach ($products as $product) {
                // TODO: Is product url path can be empty? (@TODO: UrlRewrite)

                $product->setData('save_rewrites_history', $category->getData('save_rewrites_history'));

                // TODO: hack for obtaining data from changed categories.
                // Replace on Service Data Object (@TODO: UrlRewrite)
                $this->urlSave->save($this->productUrlGenerator->generateWithChangedCategories(
                    $product,
                    [$category->getId() => $category])
                );
            }
        }
    }
}
