<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Indexer\Model;

class IndexerRegistry
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var IndexerInterface[]
     */
    protected $indexers = [];

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve indexer instance by id
     *
     * @param string $indexerId
     * @return IndexerInterface
     */
    public function get($indexerId)
    {
        if (!isset($this->indexers[$indexerId])) {
            $this->indexers[$indexerId] = $this->objectManager->create('Magento\Indexer\Model\IndexerInterface')
                ->load($indexerId);
        }
        return $this->indexers[$indexerId];
    }
}
