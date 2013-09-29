<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogInventory Stock Indexers Factory
 */
namespace Magento\CatalogInventory\Model\Resource\Indexer;

class StockFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer = 'Magento\CatalogInventory\Model\Resource\Indexer\Stock\DefaultStock';

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new indexer object
     *
     * @param string $indexerClassName
     * @param array $data
     * @return \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface
     * @throws \InvalidArgumentException
     */
    public function create($indexerClassName = '', array $data = array())
    {
        if (empty($indexerClassName)) {
            $indexerClassName = $this->_defaultIndexer;
        }
        $indexer = $this->_objectManager->create($indexerClassName, $data);
        if (false == ($indexer instanceof \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface)) {
            throw new \InvalidArgumentException($indexerClassName
                . ' doesn\'t implement \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface'
            );
        }
        return $indexer;
    }
}
