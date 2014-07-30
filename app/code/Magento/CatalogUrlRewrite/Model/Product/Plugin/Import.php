<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product\Plugin;

use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;

class Import
{
    /** @var ProductFactory  */
    protected $productFactory;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param ProductFactory $productFactory
     * @param UrlPersistInterface $urlPersist
     * @param ProductUrlGenerator $productUrlGenerator
     */
    public function __construct(
        ProductFactory $productFactory,
        UrlPersistInterface $urlPersist,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->productFactory = $productFactory;
        $this->urlPersist = $urlPersist;
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
            $product = $this->productFactory->create()->load($productId);
            $productUrls = $this->productUrlGenerator->generate($product);
            if ($productUrls) {
                $this->urlPersist->replace($productUrls);
            }
        }

        return $result;
    }
}
