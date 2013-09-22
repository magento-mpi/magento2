<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Index\Model;

/**
 * Indexer factory
 */
class IndexerFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\ConfigInterface $coreConfig
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\ConfigInterface $coreConfig
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Create new indexer object
     *
     * @param string $indexerCode
     * @param array $data
     * @throws InvalidArgumentException
     * @return \Magento\Index\Model\IndexerInterface
     */
    public function create($indexerCode, array $data = array())
    {
        $config = $this->_coreConfig->getNode(
            \Magento\Index\Model\Process::XML_PATH_INDEXER_DATA . '/' . $indexerCode
        );
        if (!$config || empty($config->model)) {
            throw new InvalidArgumentException('Indexer model for ' . $indexerCode . ' is not defined.');
        }

        $indexer = $this->_objectManager->create((string)$config->model, $data);
        if (false == ($indexer instanceof \Magento\Index\Model\IndexerInterface)) {
            throw new InvalidArgumentException(
                (string)$config->model . ' doesn\'t implement \Magento\Index\Model\IndexerInterface'
            );
        }

        return $indexer;
    }
}
