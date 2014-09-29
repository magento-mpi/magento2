<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Model\Indexer\Product\Action;

use Magento\CatalogRule\Model\Indexer\Product\ObjectWhichWorkWithCatalogRulesAndIndexer;

abstract class AbstractAction
{
    /**
     * @var \Magento\CatalogRule\Model\Indexer\Product\ObjectWhichWorkWithCatalogRulesAndIndexer
     */
    protected $objectWhichWorkCatalogRulesAndIndexers;

    public function __construct(ObjectWhichWorkWithCatalogRulesAndIndexer $objectWhichWorkCatalogRulesAndIndexers)
    {
        $this->objectWhichWorkCatalogRulesAndIndexers = $objectWhichWorkCatalogRulesAndIndexers;
    }
}
