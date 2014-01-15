<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview\View;

class Collection extends \Magento\Data\Collection
{
    /**
     * @var \Magento\Indexer\Model\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Mview\ViewFactory $entityFactory
     * @param \Magento\Mview\ConfigInterface $config
     */
    public function __construct(
        \Magento\Mview\ViewFactory $entityFactory,
        \Magento\Mview\ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($entityFactory);
    }

    /**
     * Get views
     *
     * @return array
     */
    protected function getViews()
    {
        $views = array();
        foreach ($this->config->getAll() as $data) {
            $views[] = $this->_entityFactory->create($data);
        }
        return $views;
    }

    /**
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Mview\View\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->_items) {
            $this->_items = $this->getViews();
        }
        return $this;
    }
}
