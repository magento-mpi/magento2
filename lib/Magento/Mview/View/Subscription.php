<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Mview\View;

class Subscription implements SubscriptionInterface
{
    /**
     * Trigger name qualifier
     */
    const TRIGGER_NAME_QUALIFIER = 'trg';

    /**
     * Database write connection
     *
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $write;

    /**
     * @var \Magento\DB\Ddl\Trigger
     */
    protected $triggerFactory;

    /**
     * @var \Magento\Mview\View\Collection
     */
    protected $viewCollection;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * List of views linked to the same entity as the current view
     *
     * @var array
     */
    protected $linkedViews = array();

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\DB\Ddl\TriggerFactory $triggerFactory
     * @param \Magento\Mview\View\Collection $viewCollection
     * @param \Magento\Mview\View $view
     * @param string $tableName
     * @param string $columnName
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\DB\Ddl\TriggerFactory $triggerFactory,
        \Magento\Mview\View\Collection $viewCollection,
        $view,
        $tableName,
        $columnName
    ) {
        $this->write = $resource->getConnection('core_write');
        $this->triggerFactory = $triggerFactory;
        $this->viewCollection = $viewCollection;
        $this->view = $view;
        $this->tableName = $tableName;
        $this->columnName = $columnName;

        // Force collection clear
        $this->viewCollection->clear();
    }

    /**
     * Create subsciption
     *
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function create()
    {
        foreach (\Magento\DB\Ddl\Trigger::getListOfEvents() as $event) {
            $triggerName = $this->getTriggerName(
                $this->getTableName(),
                \Magento\DB\Ddl\Trigger::TIME_AFTER,
                $event
            );

            /** @var \Magento\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(\Magento\DB\Ddl\Trigger::TIME_AFTER)
                ->setEvent($event)
                ->setTable($this->getTableName());

            $trigger->addStatement(
                $this->buildStatement($event, $this->getView()->getChangelog())
            );

            // Add statements for linked views
            foreach ($this->getLinkedViews() as $view) {
                /** @var \Magento\Mview\View $view */
                $trigger->addStatement(
                    $this->buildStatement($event, $view->getChangelog())
                );
            }

            $this->write->dropTrigger($trigger->getName());
            $this->write->createTrigger($trigger);
        }

        return $this;
    }

    /**
     * Remove subscription
     *
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function remove()
    {
        foreach (\Magento\DB\Ddl\Trigger::getListOfEvents() as $event) {
            $triggerName = $this->getTriggerName(
                $this->getTableName(),
                \Magento\DB\Ddl\Trigger::TIME_AFTER,
                $event
            );

            /** @var \Magento\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(\Magento\DB\Ddl\Trigger::TIME_AFTER)
                ->setEvent($event)
                ->setTable($this->getTableName());

            // Add statements for linked views
            foreach ($this->getLinkedViews() as $view) {
                /** @var \Magento\Mview\View $view */
                $trigger->addStatement(
                    $this->buildStatement($event, $view->getChangelog())
                );
            }

            $this->write->dropTrigger($trigger->getName());

            // Re-create trigger if trigger used by linked views
            if ($trigger->getStatements()) {
                $this->write->createTrigger($trigger);
            }
        }

        return $this;
    }

    /**
     * Retrieve list of linked views
     *
     * @return array
     */
    protected function getLinkedViews()
    {
        if (!$this->linkedViews) {
            $viewList = $this->viewCollection
                ->getViewsByStateMode(\Magento\Mview\View\StateInterface::MODE_ENABLED);

            foreach ($viewList as $view) {
                // Skip the current view
                if ($view->getId() == $this->getView()->getId()) {
                    continue;
                }
                // Search in view subscriptions
                foreach ($view->getSubscriptions() as $subscription) {
                    if ($subscription['name'] != $this->getTableName()) {
                        continue;
                    }
                    $this->linkedViews[] = $view;
                }
            }
        }
        return $this->linkedViews;
    }

    /**
     * Build trigger statement for INSER, UPDATE, DELETE events
     *
     * @param string $event
     * @param \Magento\Mview\View\ChangelogInterface $changelog
     * @return string
     */
    protected function buildStatement($event, $changelog)
    {
        switch ($event) {
            case \Magento\DB\Ddl\Trigger::EVENT_INSERT:
            case \Magento\DB\Ddl\Trigger::EVENT_UPDATE:
                return sprintf("INSERT IGNORE INTO %s (%s) VALUES (NEW.%s);",
                    $this->write->quoteIdentifier($changelog->getName()),
                    $this->write->quoteIdentifier($changelog->getColumnName()),
                    $this->write->quoteIdentifier($this->getColumnName())
                );

            case \Magento\DB\Ddl\Trigger::EVENT_DELETE:
                return sprintf("INSERT IGNORE INTO %s (%s) VALUES (OLD.%s);",
                    $this->write->quoteIdentifier($changelog->getName()),
                    $this->write->quoteIdentifier($changelog->getColumnName()),
                    $this->write->quoteIdentifier($this->getColumnName())
                );

            default:
                return '';
        }
    }

    /**
     * Retrieve trigger name
     *
     * Build a trigger name by concatenating trigger name prefix, table name,
     * trigger time and trigger event.
     *
     * @param string $tableName
     * @param string $time
     * @param string $event
     * @return string
     */
    protected function getTriggerName($tableName, $time, $event)
    {
        return self::TRIGGER_NAME_QUALIFIER . '_' . $tableName
            . '_' . $time
            . '_' . $event;
    }

    /**
     * Retrieve View related to subscription
     *
     * @return \Magento\Mview\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Retrieve table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Retrieve table column name
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }
}
