<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product\Action;

use Magento\CatalogRule\CatalogRuleException;

class Rows extends AbstractAction
{
    /**
     * TODO: think about single interface
     * Execute Rows reindex
     *
     * @param array $productIds
     * @throws CatalogRuleException
     */
    public function execute(array $productIds)
    {
        if (!$productIds) {
            throw new CatalogRuleException(__('Could not rebuild index for empty products array'));
        }
        try {
            $this->objectWhichWorkCatalogRulesAndIndexers->reindexByIds((array)$productIds);
        } catch (\Exception $e) {
            throw new CatalogRuleException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
