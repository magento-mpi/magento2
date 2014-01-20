<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

/**
 * @method string getActionClass()
 * @method array getSubscriptions()
 */
class View extends \Magento\Object implements ViewInterface
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
     * @var View\ChangelogFactory
     */
    protected $changelogFactory;

    /**
     * @var View\SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var \Magento\Mview\View\StateInterface
     */
    protected $state;

    /**
     * @param ConfigInterface $config
     * @param ActionFactory $actionFactory
     * @param View\StateFactory $stateFactory
     * @param View\ChangelogFactory $changelogFactory
     * @param View\SubscriptionFactory $subscriptionFactory
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        View\StateFactory $stateFactory,
        View\ChangelogFactory $changelogFactory,
        View\SubscriptionFactory $subscriptionFactory,
        array $data = array()
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;
        $this->stateFactory = $stateFactory;
        $this->changelogFactory = $changelogFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($data);
    }

    /**
     * Fill view data from config
     *
     * @param string $viewId
     * @return \Magento\Mview\ViewInterface
     * @throws \InvalidArgumentException
     */
    public function load($viewId)
    {
        $view = $this->config->get($viewId);
        if (empty($view) || empty($view['view_id']) || $view['view_id'] != $viewId) {
            throw new \InvalidArgumentException("{$viewId} view does not exist.");
        }

        $this->setId($viewId);
        $this->setData($view);

        return $this;
    }

    /**
     * Create subscriptions
     *
     * @throws \Exception
     * @return \Magento\Mview\ViewInterface
     */
    public function subscribe()
    {
        try {
            // Create changelog table
            $this->getChangelog()->create();

            // Create subscriptions
            foreach ($this->getSubscriptions() as $subscription) {
                /** @var \Magento\Mview\View\SubscriptionInterface $subscription */
                $subscription = $this->subscriptionFactory->create(array(
                    'view' => $this,
                    'tableName' => $subscription['name'],
                    'columnName' => $subscription['column'],
                ));
                $subscription->create();
            }

            // Update view state
            $this->getState()
                ->setMode(\Magento\Mview\View\StateInterface::MODE_ENABLED)
                ->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @throws \Exception
     * @return \Magento\Mview\ViewInterface
     */
    public function unsubscribe()
    {
        try {
            // Remove subscriptions
            foreach ($this->getSubscriptions() as $subscription) {
                /** @var \Magento\Mview\View\SubscriptionInterface $subscription */
                $subscription = $this->subscriptionFactory->create(array(
                    'view' => $this,
                    'tableName' => $subscription['name'],
                    'columnName' => $subscription['column'],
                ));
                $subscription->remove();
            }

            // Drop changelog table
            $this->getChangelog()->drop();

            // Update view state
            $this->getState()
                ->setMode(\Magento\Mview\View\StateInterface::MODE_DISABLED)
                ->save();
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
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
     * @return \Magento\Mview\ViewInterface
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

    /**
     * Retrieve linked changelog
     *
     * @return View\ChangelogInterface
     */
    public function getChangelog()
    {
        return $this->changelogFactory->create(array('viewId' => $this->getId()));
    }
}
