<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Indexer;

class Collection extends \Magento\Data\Collection
{
    /**
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = 'Magento\Indexer\Model\Indexer';

    /**
     * @var \Magento\Indexer\Model\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Indexer\Model\Resource\Indexer\State\CollectionFactory
     */
    protected $statesFactory;

    /**
     * @param \Magento\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Indexer\Model\ConfigInterface $config
     * @param \Magento\Indexer\Model\Resource\Indexer\State\CollectionFactory $statesFactory
     */
    public function __construct(
        \Magento\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Indexer\Model\ConfigInterface $config,
        \Magento\Indexer\Model\Resource\Indexer\State\CollectionFactory $statesFactory
    ) {
        $this->config = $config;
        $this->statesFactory = $statesFactory;
        parent::__construct($entityFactory);
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Indexer\Model\Indexer\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $states = $this->statesFactory->create();
            foreach (array_keys($this->config->getAll()) as $indexerId) {
                /** @var \Magento\Indexer\Model\Indexer $indexer */
                $indexer = $this->getNewEmptyItem();
                $indexer->load($indexerId);
                foreach ($states->getItems() as $state) {
                    /** @var \Magento\Indexer\Model\Indexer\State $state */
                    if ($state->getIndexerId() == $indexerId) {
                        $indexer->setState($state);
                        break;
                    }
                }
                $this->_addItem($indexer);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Return indexers by given state status
     *
     * @param string $status
     * @return \Magento\Indexer\Model\Indexer[]
     */
    public function getIndexersByStateStatus($status)
    {
        $this->load();

        $result = array();
        foreach ($this as $indexer) {
            /** @var \Magento\Indexer\Model\Indexer $indexer */
            if ($indexer->getState()->getStatus() == $status) {
                $result[] = $indexer;
            }
        }
        return $result;
    }
}
