<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

/**
 * @method int getViewId()
 * @method string getActionClass()
 * @method string getTitle()
 * @method string getDescription()
 */
class Indexer extends \Magento\Object
{
    /**
     * @var string
     */
    protected $_idFieldName = 'indexer_id';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Mview\View
     */
    protected $view;

    /**
     * @var \Magento\Indexer\Model\Indexer\StateFactory
     */
    protected $stateFactory;

    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $state;

    /**
     * @var Indexer\CollectionFactory
     */
    protected $indexersFactory;

    /**
     * @param ConfigInterface $config
     * @param ActionFactory $actionFactory
     * @param \Magento\Mview\ViewInterface $view
     * @param Indexer\StateFactory $stateFactory
     * @param Indexer\CollectionFactory $indexersFactory
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        \Magento\Mview\ViewInterface $view,
        Indexer\StateFactory $stateFactory,
        Indexer\CollectionFactory $indexersFactory,
        array $data = array()
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;
        $this->view = $view;
        $this->stateFactory = $stateFactory;
        $this->indexersFactory = $indexersFactory;
        parent::__construct($data);
    }

    /**
     * Fill indexer data from config
     *
     * @param string $indexerId
     * @return \Magento\Indexer\Model\Indexer
     * @throws \InvalidArgumentException
     */
    public function load($indexerId)
    {
        $indexer = $this->config->getIndexer($indexerId);
        if (empty($indexer) || empty($indexer['indexer_id']) || $indexer['indexer_id'] != $indexerId) {
            throw new \InvalidArgumentException("{$indexerId} indexer does not exist.");
        }

        $this->setId($indexerId);
        $this->setData($indexer);

        return $this;
    }

    /**
     * Return related view object
     *
     * @return \Magento\Mview\View
     */
    public function getView()
    {
        if (!$this->view->getId()) {
            $this->view->load($this->getViewId());
        }
        return $this->view;
    }

    /**
     * Return related state object
     *
     * @return Indexer\State
     */
    public function getState()
    {
        if (!$this->state) {
            $this->state = $this->stateFactory->create();
            $this->state->load($this->getId(), 'indexer_id');
            if (!$this->state->getId()) {
                $this->state->setIndexerId($this->getId());
            }
        }
        return $this->state;
    }

    /**
     * Set indexer state object
     *
     * @param Indexer\State $state
     * @return Indexer
     */
    public function setState(Indexer\State $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Return indexer mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getView()->getMode();
    }

    /**
     * Turn changelog mode of
     *
     * @return string
     */
    public function turnViewOff()
    {
        $this->getView()->unsubscribe();
        $this->getState()->save();
    }

    /**
     * Turn changelog mode on
     *
     * @return string
     */
    public function turnViewOn()
    {
        $this->getView()->subscribe();
        $this->getState()->save();
    }

    /**
     * Return indexer status
     *
     * @return string
     */
    public function getStatus()
    {
        if ($this->getView()->getMode() == \Magento\Mview\View\StateInterface::MODE_ENABLED
            && $this->getView()->getStatus() == \Magento\Mview\View\StateInterface::STATUS_WORKING
        ) {
            return \Magento\Indexer\Model\Indexer\State::STATUS_WORKING;
        }
        return $this->getState()->getStatus();
    }

    /**
     * Return indexer updated time
     *
     * @return string
     */
    public function getUpdated()
    {
        if ($this->getView()->getMode() == \Magento\Mview\View\StateInterface::MODE_ENABLED
            && $this->getView()->getUpdated()
        ) {
            if (!$this->getState()->getUpdated()) {
                return $this->getView()->getUpdated();
            }
            $indexerUpdatedDate = new \Zend_Date($this->getState()->getUpdated());
            $viewUpdatedDate = new \Zend_Date($this->getView()->getUpdated());
            if ($viewUpdatedDate->compare($indexerUpdatedDate) == 1) {
                return $this->getView()->getUpdated();
            }
        }
        return $this->getState()->getUpdated();
    }

    /**
     * Return indexer action instance
     *
     * @return ActionInterface
     */
    protected function getActionInstance()
    {
        return $this->actionFactory->get($this->getActionClass());
    }

    /**
     * Regenerate full index
     *
     * @throws \Exception
     */
    public function reindexAll()
    {
        if ($this->getState()->getStatus() != Indexer\State::STATUS_WORKING) {
            $this->getState()
                ->setStatus(Indexer\State::STATUS_WORKING)
                ->save();
            try {
                $this->getActionInstance()->executeFull();
                $this->getState()
                    ->setStatus(Indexer\State::STATUS_VALID)
                    ->save();
            } catch (\Exception $exception) {
                $this->getState()
                    ->setStatus(Indexer\State::STATUS_INVALID)
                    ->save();
                throw $exception;
            }
        }
    }

    /**
     * Regenerate one row in index by ID
     *
     * @param int $id
     */
    public function reindexRow($id)
    {
        $this->getActionInstance()->executeRow($id);
    }

    /**
     * Regenerate rows in index by ID list
     *
     * @param int[] $ids
     */
    public function reindexList($ids)
    {
        $this->getActionInstance()->executeList($ids);
    }
}
