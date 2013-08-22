<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Index
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Indexer strategy
 */
class Magento_Index_Model_Indexer
{
    /**
     * Collection of available processes
     *
     * @var Magento_Index_Model_Resource_Process_Collection
     */
    protected $_processesCollection;

    /**
     * Class constructor. Initialize index processes based on configuration
     */
    public function __construct()
    {
        $this->_processesCollection = $this->_createCollection();
    }

    /**
     * @return Magento_Index_Model_Resource_Process_Collection
     */
    private function _createCollection()
    {
        return Mage::getResourceModel('Magento_Index_Model_Resource_Process_Collection');
    }

    /**
     * Get collection of all available processes
     *
     * @return Magento_Index_Model_Resource_Process_Collection
     */
    public function getProcessesCollection()
    {
        return $this->_processesCollection;
    }


    /**
     * Get index process by specific id
     *
     * @param int $processId
     * @return Magento_Index_Model_Process | false
     */
    public function getProcessById($processId)
    {
        foreach ($this->_processesCollection as $process) {
            if ($process->getId() == $processId) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Get index process by specific code
     *
     * @param string $code
     * @return Magento_Index_Model_Process | false
     */
    public function getProcessByCode($code)
    {
        foreach ($this->_processesCollection as $process) {
            if ($process->getIndexerCode() == $code) {
                return $process;
            }
        }
        return false;
    }

    /**
     * Indexing all pending events.
     * Events set can be limited by event entity and type
     *
     * @param   null | string $entity
     * @param   null | string $type
     * @return  Magento_Index_Model_Indexer
     */
    public function indexEvents($entity=null, $type=null)
    {
        Mage::dispatchEvent('start_index_events' . $this->_getEventTypeName($entity, $type));
        /** @var $resourceModel Magento_Index_Model_Resource_Process */
        $resourceModel = Mage::getResourceSingleton('Magento_Index_Model_Resource_Process');
        $resourceModel->beginTransaction();
        try {
            $this->_runAll('indexEvents', array($entity, $type));
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
        Mage::dispatchEvent('end_index_events' . $this->_getEventTypeName($entity, $type));
        return $this;
    }

    /**
     * Index one event by all processes
     *
     * @param   Magento_Index_Model_Event $event
     * @return  Magento_Index_Model_Indexer
     */
    public function indexEvent(Magento_Index_Model_Event $event)
    {
        $this->_runAll('safeProcessEvent', array($event));
        return $this;
    }

    /**
     * Register event in each indexing process process
     *
     * @param Magento_Index_Model_Event $event
     */
    public function registerEvent(Magento_Index_Model_Event $event)
    {
        $this->_runAll('register', array($event));
        return $this;
    }

    /**
     * Create new event log and register event in all processes
     *
     * @param   Magento_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @param   bool $doSave
     * @return  Magento_Index_Model_Event
     */
    public function logEvent(Magento_Object $entity, $entityType, $eventType, $doSave=true)
    {
        $event = Mage::getModel('Magento_Index_Model_Event')
            ->setEntity($entityType)
            ->setType($eventType)
            ->setDataObject($entity)
            ->setEntityPk($entity->getId());

        $this->registerEvent($event);
        if ($doSave) {
            $event->save();
        }
        return $event;
    }

    /**
     * Create new event log and register event in all processes.
     * Initiate events indexing procedure.
     *
     * @param   Magento_Object $entity
     * @param   string $entityType
     * @param   string $eventType
     * @return  Magento_Index_Model_Indexer
     */
    public function processEntityAction(Magento_Object $entity, $entityType, $eventType)
    {
        $event = $this->logEvent($entity, $entityType, $eventType, false);
        /**
         * Index and save event just in case if some process matched it
         */
        if ($event->getProcessIds()) {
            Mage::dispatchEvent('start_process_event' . $this->_getEventTypeName($entityType, $eventType));
            /** @var $resourceModel Magento_Index_Model_Resource_Process */
            $resourceModel = Mage::getResourceSingleton('Magento_Index_Model_Resource_Process');
            $resourceModel->beginTransaction();
            try {
                $this->indexEvent($event);
                $resourceModel->commit();
            } catch (Exception $e) {
                $resourceModel->rollBack();
                throw $e;
            }
            $event->save();
            Mage::dispatchEvent('end_process_event' . $this->_getEventTypeName($entityType, $eventType));
        }
        return $this;
    }

    /**
     * Reindex all processes
     */
    public function reindexAll()
    {
        $this->_reindexCollection($this->_createCollection());
    }

    /**
     * Reindex only processes that are invalidated
     */
    public function reindexRequired()
    {
        $collection = $this->_createCollection();
        $collection->addFieldToFilter('status', Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        $this->_reindexCollection($collection);
    }

    /**
     * Sub-routine for iterating collection and reindexing all processes of specified collection
     *
     * @param Magento_Index_Model_Resource_Process_Collection $collection
     */
    private function _reindexCollection(Magento_Index_Model_Resource_Process_Collection $collection)
    {
        /** @var $process Magento_Index_Model_Process */
        foreach ($collection as $process) {
            $process->reindexEverything();
        }
    }

    /**
     * Run all processes method with parameters
     * Run by depends priority
     * Not recursive call is not implement
     *
     * @param string $method
     * @param array $args
     * @return Magento_Index_Model_Indexer
     */
    protected function _runAll($method, $args)
    {
        $checkLocks = $method != 'register';
        $processed = array();
        foreach ($this->_processesCollection as $process) {
            $code = $process->getIndexerCode();
            if (in_array($code, $processed)) {
                continue;
            }
            $hasLocks = false;

            if ($process->getDepends()) {
                foreach ($process->getDepends() as $processCode) {
                    $dependProcess = $this->getProcessByCode($processCode);
                    if ($dependProcess && !in_array($processCode, $processed)) {
                        if ($checkLocks && $dependProcess->isLocked()) {
                            $hasLocks = true;
                        } else {
                            call_user_func_array(array($dependProcess, $method), $args);
                            if ($checkLocks && $dependProcess->getMode() == Magento_Index_Model_Process::MODE_MANUAL) {
                                $hasLocks = true;
                            } else {
                                $processed[] = $processCode;
                            }
                        }
                    }
                }
            }

            if (!$hasLocks) {
                call_user_func_array(array($process, $method), $args);
                $processed[] = $code;
            }
        }
    }

    /**
     * Get event type name
     *
     * @param null|string $entityType
     * @param null|string $eventType
     * @return string
     */
    protected function _getEventTypeName($entityType = null, $eventType = null)
    {
        $eventName = $entityType . '_' . $eventType;
        $eventName = trim($eventName, '_');
        if (!empty($eventName)) {
            $eventName = '_' . $eventName;
        }
        return $eventName;
    }
}
