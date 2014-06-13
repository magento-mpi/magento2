<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Model\Indexer\Stock\Plugin;

abstract class AbstractPlugin
{
    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Processor
     */
    protected $_indexerProcessor;

    /**
     * @param \Magento\CatalogInventory\Model\Indexer\Stock\Processor  $indexerProcessor
     */
    public function __construct(\Magento\CatalogInventory\Model\Indexer\Stock\Processor $indexerProcessor)
    {
        $this->_indexerProcessor = $indexerProcessor;
    }
}
