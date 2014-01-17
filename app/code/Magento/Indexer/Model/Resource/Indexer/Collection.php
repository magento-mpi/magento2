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
     * @param \Magento\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Indexer\Model\ConfigInterface $config
     */
    public function __construct(
        \Magento\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Indexer\Model\ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($entityFactory);
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
        if (!$this->isLoaded()) {
            foreach (array_keys($this->config->getAll()) as $indexerId) {
                /** @var \Magento\Indexer\Model\Indexer $indexer */
                $indexer = $this->getNewEmptyItem();
                $indexer->load($indexerId);
                $this->_addItem($indexer);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }
}
