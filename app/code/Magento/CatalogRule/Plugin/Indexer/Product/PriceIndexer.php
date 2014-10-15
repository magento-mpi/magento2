<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Plugin\Indexer\Product;


class PriceIndexer
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $priceProcessor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $priceProcessor
     */
    public function __construct(\Magento\Catalog\Model\Indexer\Product\Price\Processor $priceProcessor)
    {
        $this->priceProcessor = $priceProcessor;
    }

    public function afterReindexFull()
    {
        $this->priceProcessor->markIndexerAsInvalid();
    }
}
