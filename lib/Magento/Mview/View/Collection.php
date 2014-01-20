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
     * Item object class name
     *
     * @var string
     */
    protected $_itemObjectClass = 'Magento\Mview\ViewInterface';

    /**
     * @var \Magento\Indexer\Model\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Mview\View\State\CollectionFactory
     */
    protected $statesFactory;

    /**
     * @param \Magento\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Magento\Mview\ConfigInterface $config
     * @param State\CollectionFactory $statesFactory
     */
    public function __construct(
        \Magento\Data\Collection\EntityFactoryInterface $entityFactory,
        \Magento\Mview\ConfigInterface $config,
        \Magento\Mview\View\State\CollectionFactory $statesFactory
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
     * @return \Magento\Mview\View\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $states = $this->statesFactory->create();
            foreach (array_keys($this->config->getAll()) as $viewId) {
                /** @var \Magento\Mview\ViewInterface $view */
                $view = $this->getNewEmptyItem();
                $view->load($viewId);
                foreach ($states->getItems() as $state) {
                    /** @var \Magento\Mview\View\StateInterface $state */
                    if ($state->getViewId() == $viewId) {
                        $view->setState($state);
                        break;
                    }
                }
                $this->_addItem($view);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Return views by given state mode
     *
     * @param string $mode
     * @return \Magento\Mview\ViewInterface[]
     */
    public function getViewsByStateMode($mode)
    {
        $this->load();

        $result = array();
        foreach ($this as $view) {
            /** @var \Magento\Mview\ViewInterface $view */
            if ($view->getState()->getMode() == $mode) {
                $result[] = $view;
            }
        }
        return $result;
    }

    /**
     * Return views by given state status
     *
     * @param string $status
     * @return \Magento\Mview\ViewInterface[]
     */
    public function getViewsByStateStatus($status)
    {
        $this->load();

        $result = array();
        foreach ($this as $view) {
            /** @var \Magento\Mview\ViewInterface $view */
            if ($view->getState()->getStatus() == $status) {
                $result[] = $view;
            }
        }
        return $result;
    }
}
