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
     * @param \Magento\App\Resource $resource
     * @param \Magento\DB\Ddl\TriggerFactory $triggerFactory
     * @param \Magento\Mview\View\Collection $viewCollection
     * @param \Magento\Mview\ViewInterface $view
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
    }

    /**
     * Create subsciption
     *
     * @return \Magento\Mview\View\SubscriptionInterface
     */
    public function create()
    {
        foreach (\Magento\DB\Ddl\Trigger::getListOfEvents() as $event) {
            /** @var \Magento\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create();

            $triggerName = $this->getTriggerName($this->getTableName(), \Magento\DB\Ddl\Trigger::TIME_AFTER, $event);
            $trigger->setName($triggerName);
            $trigger->setTime(\Magento\DB\Ddl\Trigger::TIME_AFTER);
            $trigger->setEvent($event);
            $trigger->setTable($this->getTableName());

            // Add statement for current subscription
            $trigger->addStatement(
                $this->buildStatement($event, $this->getView()->getChangelog())
            );

            // Add statements for linked views
            foreach ($this->getLinkedViews() as $view) {
                /** @var \Magento\Mview\ViewInterface $view */
                $trigger->addStatement(
                    $this->buildStatement($event, $view->getChangelog())
                );
            }

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
            /** @var \Magento\DB\Ddl\Trigger $trigger */
            $trigger = $this->triggerFactory->create();

            $triggerName = $this->getTriggerName($this->getTableName(), \Magento\DB\Ddl\Trigger::TIME_AFTER, $event);
            $trigger->setName($triggerName);

            $this->write->dropTrigger($trigger);
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
        $result = array();

        $viewList = $this->viewCollection->getViewsByStateMode(\Magento\Mview\View\StateInterface::MODE_ENABLED);

        foreach ($viewList as $view) {
            foreach ($view->getSubscriptions() as $subscription) {
                if ($subscription['name'] != $this->getTableName()) {
                    continue;
                }
                $result[] = $view;
            }
        }

        return $result;
    }

    /**
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
     * Retrieve fully qualified trigger name
     *
     * Build a trigger name by concatenating name prefix, table name,
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
     * Retrieve View object related to subscription
     *
     * @return \Magento\Mview\ViewInterface
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Retrieve table name
     *
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Retrieve table column name
     *
     * @return mixed
     */
    public function getColumnName()
    {
        return $this->columnName;
    }
}
