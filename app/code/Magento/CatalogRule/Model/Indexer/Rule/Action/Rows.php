<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Rule\Action;

use Magento\CatalogRule\CatalogRuleException;

class Rows extends AbstractAction
{
    /**
     * TODO: think about single interface
     * Execute Row reindex
     *
     * @param array $productIds
     * @throws CatalogRuleException
     */
    public function execute($productIds)
    {
        if (!$productIds) {
            throw new CatalogRuleException(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->objectWhichWorkCatalogRulesAndIndexers->reindexByIds((array)$productIds);
        } catch (\Exception $e) {
            throw new CatalogRuleException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
