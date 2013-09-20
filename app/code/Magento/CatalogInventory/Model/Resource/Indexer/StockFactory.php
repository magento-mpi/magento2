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
class Magento_CatalogInventory_Model_Resource_Indexer_StockFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer = 'Magento_CatalogInventory_Model_Resource_Indexer_Stock_Default';

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_ConfigInterface $coreConfig
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_ConfigInterface $coreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Create new indexer object
     *
     * @param string $indexerClassName
     * @param array $data
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock_Interface
     * @throws InvalidArgumentException
     */
    public function create($indexerClassName = '', array $data = array())
    {
        if (empty($indexerClassName)) {
            $indexerClassName = $this->_defaultIndexer;
        }
        $indexer = $this->_objectManager->create($indexerClassName, $data);
        if (false == ($indexer instanceof Magento_CatalogInventory_Model_Resource_Indexer_Stock_Interface)) {
            throw new InvalidArgumentException($indexerClassName
                . ' doesn\'t implement Magento_CatalogInventory_Model_Resource_Indexer_Stock_Interface'
            );
        }
        return $indexer;
    }
}