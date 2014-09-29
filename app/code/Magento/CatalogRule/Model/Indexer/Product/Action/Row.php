<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product\Action;

use Magento\CatalogRule\CatalogRuleException;

class Row extends AbstractAction
{
    /**
     * TODO: think about single interface
     * Execute Row reindex
     *
     * @param int $productId
     * @throws CatalogRuleException
     */
    public function execute($productId)
    {
        if (!$productId) {
            throw new CatalogRuleException(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->objectWhichWorkCatalogRulesAndIndexers->reindexById($productId);
        } catch (\Exception $e) {
            throw new CatalogRuleException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
