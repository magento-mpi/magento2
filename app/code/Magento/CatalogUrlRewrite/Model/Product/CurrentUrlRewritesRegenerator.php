<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\CatalogUrlRewrite\Model\ObjectRegistry;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;

class CurrentUrlRewritesRegenerator
{
    /** @var UrlFinderInterface */
    protected $urlFinder;

    /** @var \Magento\Catalog\Model\Product */
    protected $product;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistry */
    protected $productCategories;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param UrlFinderInterface $urlFinder
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder $urlRewriteBuilder
     */
    public function __construct(
        UrlFinderInterface $urlFinder,
        ProductUrlPathGenerator $productUrlPathGenerator,
        UrlRewriteBuilder $urlRewriteBuilder
    ) {
        $this->urlFinder = $urlFinder;
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->urlRewriteBuilder = $urlRewriteBuilder;
    }

    /**
     * Generate list based on current rewrites
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistry $productCategories
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($storeId, Product $product, ObjectRegistry $productCategories)
    {
        $this->product = $product;
        $this->productCategories = $productCategories;

        $currentUrlRewrites = $this->urlFinder->findAllByData(
            [
                UrlRewrite::STORE_ID => $storeId,
                UrlRewrite::ENTITY_ID => $this->product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
            ]
        );

        $urlRewrites = [];
        foreach ($currentUrlRewrites as $currentUrlRewrite) {
            $urlRewrites = array_merge(
                $urlRewrites,
                $currentUrlRewrite->getIsAutogenerated() ? $this->generateForAutogenerated($currentUrlRewrite, $storeId)
                    : $this->generateForCustom($currentUrlRewrite, $storeId)
            );
        }
        return $urlRewrites;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @param int $storeId
     * @return array
     */
    protected function generateForAutogenerated($url, $storeId)
    {
        $urls = [];
        if ($this->product->getData('save_rewrites_history')) {
            $category = $this->retrieveCategoryFromMetadata($url);
            $targetPath = $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId, $category);
            if ($url->getRequestPath() !== $targetPath) {
                $urls[] = $this->urlRewriteBuilder
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($this->product->getId())
                    ->setRequestPath($url->getRequestPath())
                    ->setTargetPath($targetPath)
                    ->setStoreId($storeId)
                    ->setRedirectType(OptionProvider::PERMANENT)
                    ->setIsAutogenerated(0)
                    ->setMetadata($url->getMetadata())
                    ->create();
            }
        }
        return $urls;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @param int $storeId
     * @return array
     */
    protected function generateForCustom($url, $storeId)
    {
        $urls = [];
        $category = $this->retrieveCategoryFromMetadata($url);
        if ($category === false) {
            return $urls;
        }
        $targetPath = $url->getRedirectType()
            ? $this->productUrlPathGenerator->getUrlPathWithSuffix($this->product, $storeId, $category)
            : $url->getTargetPath();
        if ($url->getRequestPath() !== $targetPath) {
            $urls[] = $this->urlRewriteBuilder
                ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($this->product->getId())
                ->setRequestPath($url->getRequestPath())
                ->setTargetPath($targetPath)
                ->setRedirectType($url->getRedirectType())
                ->setStoreId($storeId)
                ->setDescription($url->getDescription())
                ->setIsAutogenerated(0)
                ->setMetadata($url->getMetadata())
                ->create();
        }
        return $urls;
    }

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewrite $url
     * @return \Magento\Catalog\Model\Category|null|bool
     */
    protected function retrieveCategoryFromMetadata($url)
    {
        $metadata = $url->getMetadata();
        if (isset($metadata['category_id'])) {
            $category = $this->productCategories->get($metadata['category_id']);
            return $category === null ? false : $category;
        }
        return null;
    }
}
