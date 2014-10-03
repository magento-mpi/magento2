<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Rule\Action;

class Full extends AbstractAction
{
    /**
     * TODO: think about single interface
     * Full Row reindex
     */
    public function execute()
    {
        try {
            $this->objectWhichWorkCatalogRulesAndIndexers->reindexAll();
        } catch (\Exception $e) {
            throw new CatalogRuleException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
 