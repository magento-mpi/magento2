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
namespace Magento\Index\Model\Indexer;

class Factory
{
    /**
     * @var \Magento\ObjectManager|null
     */
    protected $_objectManager = null;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $indexerInstanceName
     * @return \Magento\Index\Model\Indexer\AbstractIndexer|null
     */
    public function create($indexerInstanceName)
    {
        if ($indexerInstanceName) {
            return $this->_objectManager->create($indexerInstanceName);
        }

        return null;
    }
}
