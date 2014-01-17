<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

/**
 * @method int getViewId()
 * @method string getActionClass()
 * @method array getSubscriptions()
 */
class View extends \Magento\Object
{
    /**
     * @var string
     */
    protected $_idFieldName = 'view_id';

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Mview\View\StateFactory
     */
    protected $stateFactory;

    /**
     * @var \Magento\Mview\View\StateInterface
     */
    protected $state;

    /**
     * @param ConfigInterface $config
     * @param ActionFactory $actionFactory
     * @param View\StateFactory $stateFactory
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        View\StateFactory $stateFactory,
        array $data = array()
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;
        $this->stateFactory = $stateFactory;
        parent::__construct($data);
    }

    /**
     * Fill view data from config
     *
     * @param string $viewId
     * @return \Magento\Mview\View
     * @throws \InvalidArgumentException
     */
    public function load($viewId)
    {
        $view = $this->config->get($viewId);
        if (empty($view) || empty($view['viewId']) || $view['viewId'] != $viewId) {
            throw new \InvalidArgumentException("{$viewId} view does not exist.");
        }
        $this->setId($viewId);
        $this->setData($view);
        return $this;
    }

    public function subscribe()
    {
        foreach ($this->getSubscriptions() as $table) {
        }
    }

    /**
     * Materialize view by IDs in changelog
     */
    public function update()
    {
        $ids = array(); // TODO: Use changelog
        $action = $this->actionFactory->create($this->getActionClass());
        $action->execute($ids);
    }

    /**
     * Return related state object
     *
     * @return View\StateInterface
     */
    public function getState()
    {
        if (!$this->state) {
            $this->state = $this->stateFactory->create();
            $this->state->loadByView($this->getId());
        }
        return $this->state;
    }

    /**
     * Set view state object
     *
     * @param View\StateInterface $state
     * @return \Magento\Mview\View
     */
    public function setState(View\StateInterface $state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Return view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getState()->getMode();
    }

    /**
     * Return view status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getState()->getStatus();
    }

}