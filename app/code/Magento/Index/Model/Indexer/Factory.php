<?php
/**
 * {license_notice}
 *
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
     * @var \Magento\Framework\ObjectManager|null
     */
    protected $_objectManager = null;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
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
