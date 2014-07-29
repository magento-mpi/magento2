<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product\Plugin;

use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;

class Import
{
    /** @var ProductFactory  */
    protected $productFactory;

    /** @var CategoryFactory  */
    protected $categoryFactory;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var FilterFactory */
    protected $filterFactory;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param ProductFactory $productFactory
     * @param CategoryFactory $categoryFactory
     * @param UrlPersistInterface $urlPersist
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        ProductFactory $productFactory,
        CategoryFactory $categoryFactory,
        UrlPersistInterface $urlPersist,
        FilterFactory $filterFactory,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->urlPersist = $urlPersist;
        $this->filterFactory = $filterFactory;
        $this->productUrlGenerator = $productUrlGenerator;
    }

    /**
     * @param \Magento\CatalogImportExport\Model\Import\Product $import
     * @param bool $result
     * @return bool
     */
    public function afterImportData(\Magento\CatalogImportExport\Model\Import\Product $import, $result)
    {
        foreach ($import->getAffectedEntityIds() as $productId) {
            $productUrls = [];
            $product = $this->productFactory->create()->load($productId);
            if ($product->getCategoryIds() === []) {
                $productUrls = $this->productUrlGenerator->generate($product);
            } else {
                foreach ($product->getCategoryIds() as $categoryId) {
                    $category = $this->categoryFactory->create()->load($categoryId);
                    $product->setStoreId($category->getStoreId());
                    $product->setStoreIds($category->getStoreIds());
                    $productUrls = $this->productUrlGenerator->generate($product);
                }
            }
            if ($productUrls) {
                $this->urlPersist->replace($productUrls);
            }
        }

        return $result;
    }
}
