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
 * @method string getGroup()
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
     * @var View\ChangelogInterface
     */
    protected $changelog;

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
     * @param View\StateInterface $state
     * @param View\ChangelogInterface $changelog
     * @param View\SubscriptionFactory $subscriptionFactory
     * @param array $data
     */
    public function __construct(
        ConfigInterface $config,
        ActionFactory $actionFactory,
        View\StateInterface $state,
        View\ChangelogInterface $changelog,
        View\SubscriptionFactory $subscriptionFactory,
        array $data = array()
    ) {
        $this->config = $config;
        $this->actionFactory = $actionFactory;
        $this->state = $state;
        $this->changelog = $changelog;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($data);
    }

    /**
     * Fill view data from config
     *
     * @param string $viewId
     * @return ViewInterface
     * @throws \InvalidArgumentException
     */
    public function load($viewId)
    {
        $view = $this->config->getView($viewId);
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
     * @return ViewInterface
     */
    public function subscribe()
    {
        if ($this->getState()->getMode() != View\StateInterface::MODE_ENABLED) {
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
                    ->setMode(View\StateInterface::MODE_ENABLED)
                    ->save();
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Remove subscriptions
     *
     * @throws \Exception
     * @return ViewInterface
     */
    public function unsubscribe()
    {
        if ($this->getState()->getMode() != View\StateInterface::MODE_DISABLED) {
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
                    ->setMode(View\StateInterface::MODE_DISABLED)
                    ->save();
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Materialize view by IDs in changelog
     *
     * @throws \Exception
     */
    public function update()
    {
        if ($this->getState()->getMode() == View\StateInterface::MODE_ENABLED
            && $this->getState()->getStatus() != View\StateInterface::STATUS_WORKING
        ) {
            $currentVersionId = $this->getChangelog()->getVersion();
            $lastVersionId = $this->getState()->getVersionId();
            $ids = $this->getChangelog()->getList($lastVersionId, $currentVersionId);
            if ($ids) {
                $action = $this->actionFactory->get($this->getActionClass());
                $this->getState()
                    ->setStatus(View\StateInterface::STATUS_WORKING)
                    ->save();
                try {
                    $action->execute($ids);
                    $this->getState()
                        ->setVersionId($currentVersionId)
                        ->setStatus(View\StateInterface::STATUS_IDLE)
                        ->save();
                } catch (\Exception $exception) {
                    $this->getState()
                        ->setStatus(View\StateInterface::STATUS_IDLE)
                        ->save();
                    throw $exception;
                }
            }
        }
    }

    /**
     * Clear precessed changelog entries
     */
    public function clearChangelog()
    {
        $this->getChangelog()->clear($this->getState()->getVersionId());
    }

    /**
     * Return related state object
     *
     * @return View\StateInterface
     */
    public function getState()
    {
        if (!$this->state->getViewId()) {
            $this->state->loadByView($this->getId());
        }
        return $this->state;
    }

    /**
     * Set view state object
     *
     * @param View\StateInterface $state
     * @return ViewInterface
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
     * Return view updated datetime
     *
     * @return string
     */
    public function getUpdated()
    {
        return $this->getState()->getUpdated();
    }

    /**
     * Retrieve linked changelog
     *
     * @return View\ChangelogInterface
     */
    public function getChangelog()
    {
        if (!$this->changelog->getViewId()) {
            $this->changelog->setViewId($this->getId());
        }
        return $this->changelog;
    }
}
