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
use Magento\ImportExport\Model\Import as ImportExport;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

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
     * @param ImportProduct $import
     * @param bool $result
     * @return bool
     */
    public function afterImportData(ImportProduct $import, $result)
    {
        if ($import->getAffectedEntityIds()) {
            foreach ($import->getAffectedEntityIds() as $productId) {
                $product = $this->productFactory->create()->load($productId);
                $productUrls = $this->productUrlGenerator->generate($product);
                if ($productUrls) {
                    $this->urlPersist->replace($productUrls);
                }
            }
        } elseif (ImportExport::BEHAVIOR_DELETE == $import->getBehavior()) {
            $this->clearProductUrls($import);
        }

        return $result;
    }

    /**
     * @param ImportProduct $import
     */
    protected function clearProductUrls(ImportProduct $import)
    {
        $oldSku = $import->getOldSku();
        while ($bunch = $import->getNextBunch()) {
            $idToDelete = [];
            foreach ($bunch as $rowNum => $rowData) {
                if ($import->validateRow($rowData, $rowNum)
                    && ImportProduct::SCOPE_DEFAULT == $import->getRowScope($rowData)
                ) {
                    $idToDelete[] = $oldSku[$rowData[ImportProduct::COL_SKU]]['entity_id'];
                }
            }
            foreach ($idToDelete as $productId) {
                $this->urlPersist->deleteByEntityData(
                    [
                        UrlRewrite::ENTITY_ID => $productId,
                        UrlRewrite::ENTITY_TYPE => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    ]
                );
            }
        }
    }
}
