<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer;

abstract class AbstractPlugin
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var string
     */
    protected $indexerCode;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param string $indexerCode
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        $indexerCode
    ) {
        $this->indexer = $indexer;
        $this->indexerCode = $indexerCode;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load($this->indexerCode);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     */
    protected function invalidateIndexer()
    {
        $this->getIndexer()->invalidate();
    }

    /**
     * Validate changes for invalidating indexer
     *
     * @param \Magento\Core\Model\AbstractModel $object
     * @return bool
     */
    abstract protected function validate(\Magento\Core\Model\AbstractModel $object);

    /**
     * Process to invalidate indexer
     *
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    public function aroundSave(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $needInvalidating = $this->validate($arguments[0]);
        $objectResource = $invocationChain->proceed($arguments);
        if ($needInvalidating) {
            $this->invalidateIndexer();
        }

        return $objectResource;
    }
}
