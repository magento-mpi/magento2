<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product\Plugin;

use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\ImportExport\Model\Import as ImportExport;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

class Import
{
    /** @var ProductFactory  */
    protected $productFactory;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ProductUrlRewriteGenerator */
    protected $productUrlRewriteGenerator;

    /**
     * @param ProductFactory $productFactory
     * @param UrlPersistInterface $urlPersist
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     */
    public function __construct(
        ProductFactory $productFactory,
        UrlPersistInterface $urlPersist,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator
    ) {
        $this->productFactory = $productFactory;
        $this->urlPersist = $urlPersist;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
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
                $productUrls = $this->productUrlRewriteGenerator->generate($product);
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
     * @return void
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
                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $productId,
                    UrlRewrite::ENTITY_TYPE => ProductUrlRewriteGenerator::ENTITY_TYPE,
                ]);
            }
        }
    }
}
