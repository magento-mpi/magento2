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
 * @method array getSubscriptions()
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
     * @var \Magento\Mview\ViewFactory
     */
    protected $viewFactory;

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
     * @param ConfigInterface $config
     * @param ActionFactory $actionFactory
     * @param \Magento\Mview\ViewFactory $viewFactory
     * @param Indexer\StateFactory $stateFactory
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        \Magento\Mview\ViewFactory $viewFactory,
        Indexer\StateFactory $stateFactory,
        array $data = array()
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;
        $this->viewFactory = $viewFactory;
        $this->stateFactory = $stateFactory;
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
        $indexer = $this->config->get($indexerId);
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
        if (!$this->view) {
            $this->view = $this->viewFactory->create()->load($this->getViewId());
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
        return $this->getState()->getUpdated();
    }
}