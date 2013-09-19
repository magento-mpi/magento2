<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Indexer factory
 */
class Magento_Index_Model_IndexerFactory
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
     * @param $indexerCode
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Index_Model_IndexerInterface
     */
    public function create($indexerCode, array $data = array())
    {
        $config = $this->_coreConfig->getNode(
            Magento_Index_Model_Process::XML_PATH_INDEXER_DATA . '/' . $indexerCode
        );
        if (!$config || empty($config->model)) {
            throw new InvalidArgumentException('Indexer model for ' . $indexerCode . ' is not defined.');
        }

        $indexer = $this->_objectManager->create((string)$config->model, $data);
        if (false == ($indexer instanceof Magento_Index_Model_IndexerInterface)) {
            throw new InvalidArgumentException(
                (string)$config->model . ' doesn\'t implement Magento_Index_Model_IndexerInterface'
            );
        }

        return $indexer;
    }
}
