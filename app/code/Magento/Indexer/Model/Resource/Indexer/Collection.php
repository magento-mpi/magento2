<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Resource\Indexer;

class Collection extends \Magento\Data\Collection
{
    /**
     * @var \Magento\Indexer\Model\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Indexer\Model\ConfigInterface $config
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Indexer\Model\ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($entityFactory);
    }

    /**
     * Get indexers
     *
     * @return array
     */
    protected function getIndexers()
    {
        $indexers = array();
        foreach ($this->config->getAll() as $data) {
            $indexers[] = new \Magento\Object($data);
        }
        return $indexers;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Indexer\Model\Resource\Indexer\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->_items) {
            $this->_items = $this->getIndexers();
        }
        return $this;
    }
}
