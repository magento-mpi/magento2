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
 * @method Magento_Index_Model_Resource_Process _getResource()
 * @method Magento_Index_Model_Resource_Process getResource()
 * @method string getIndexerCode()
 * @method Magento_Index_Model_Process setIndexerCode(string $value)
 * @method string getStatus()
 * @method Magento_Index_Model_Process setStatus(string $value)
 * @method string getStartedAt()
 * @method Magento_Index_Model_Process setStartedAt(string $value)
 * @method string getEndedAt()
 * @method Magento_Index_Model_Process setEndedAt(string $value)
 * @method string getMode()
 * @method Magento_Index_Model_Process setMode(string $value)
 *
 * @category    Magento
 * @package     Magento_Index
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Index_Model_Process extends Magento_Core_Model_Abstract
{
    const XML_PATH_INDEXER_DATA     = 'global/index/indexer';
    /**
     * Process statuses
     */
    const STATUS_RUNNING            = 'working';
    const STATUS_PENDING            = 'pending';
    const STATUS_REQUIRE_REINDEX    = 'require_reindex';

    /**
     * Process event statuses
     */
    const EVENT_STATUS_NEW          = 'new';
    const EVENT_STATUS_DONE         = 'done';
    const EVENT_STATUS_ERROR        = 'error';
    const EVENT_STATUS_WORKING      = 'working';

    /**
     * Process modes
     * Process mode allow disable automatic process events processing
     */
    const MODE_MANUAL              = 'manual';
    const MODE_REAL_TIME           = 'real_time';

    /**
     * Indexer stategy object
     *
     * @var Magento_Index_Model_Indexer_Abstract
     */
    protected $_indexer = null;

    /**
     * Lock file entity storage
     *
     * @var Magento_Index_Model_Lock_Storage
     */
    protected $_lockStorage;

    /**
     * Instance of current process file
     *
     * @var Magento_Index_Model_Process_File
     */
    protected $_processFile;

    /**
     * Event repostiory
     *
     * @var Magento_Index_Model_EventRepository
     */
    protected $_eventRepository;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Index_Model_IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @param Magento_Index_Model_IndexerFactory $indexerFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Index_Model_Lock_Storage $lockStorage
     * @param Magento_Index_Model_EventRepository $eventRepository
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Index_Model_IndexerFactory $indexerFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Index_Model_Lock_Storage $lockStorage,
        Magento_Index_Model_EventRepository $eventRepository,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_lockStorage = $lockStorage;
        $this->_eventRepository = $eventRepository;
        $this->_indexerFactory = $indexerFactory;
    }

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Index_Model_Resource_Process');
    }

    /**
     * Set indexer class name as data namespace for event object
     *
     * @param   Magento_Index_Model_Event $event
     * @return  Magento_Index_Model_Process
     */
    protected function _setEventNamespace(Magento_Index_Model_Event $event)
    {
        $namespace = get_class($this->getIndexer());
        $event->setDataNamespace($namespace);
        $event->setProcess($this);
        return $this;
    }

    /**
     * Remove indexer namespace from event
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_Process
     */
    protected function _resetEventNamespace($event)
    {
        $event->setDataNamespace(null);
        $event->setProcess(null);
        return $this;
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_Process
     */
    public function register(Magento_Index_Model_Event $event)
    {
        if ($this->matchEvent($event)) {
            $this->_setEventNamespace($event);
            $this->getIndexer()->register($event);
            $event->addProcessId($this->getId());
            $this->_resetEventNamespace($event);
            if ($this->getMode() == self::MODE_MANUAL) {
                $this->_getResource()->updateStatus($this, self::STATUS_REQUIRE_REINDEX);
            }
        }
        return $this;

    }

    /**
     * Check if event can be matched by process
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event)
    {
        return $this->getIndexer()->matchEvent($event);
    }

    /**
     * Check if specific entity and action type is matched
     *
     * @param   string $entity
     * @param   string $type
     * @return  bool
     */
    public function matchEntityAndType($entity, $type)
    {
        if ($entity !== null && $type !== null) {
            return $this->getIndexer()->matchEntityAndType($entity, $type);
        }
        return true;
    }

    /**
     * Reindex all data what this process responsible is
     *
     */
    public function reindexAll()
    {
        if ($this->isLocked()) {
            Mage::throwException(__('%1 Index process is not working now. Please try running this process later.', $this->getIndexer()->getName()));
        }

        $processStatus = $this->getStatus();

        $this->_getResource()->startProcess($this);
        $this->lock();
        try {
            $eventsCollection = $this->_eventRepository->getUnprocessed($this);

            /** @var $eventResource Magento_Index_Model_Resource_Event */
            $eventResource = Mage::getResourceSingleton('Magento_Index_Model_Resource_Event');

            if ($processStatus == self::STATUS_PENDING && $eventsCollection->getSize() > 0
                || $this->getForcePartialReindex()
            ) {
                $this->_getResource()->beginTransaction();
                try {
                    $this->_processEventsCollection($eventsCollection, false);
                    $this->_getResource()->commit();
                } catch (Exception $e) {
                    $this->_getResource()->rollBack();
                    throw $e;
                }
            } else {
                //Update existing events since we'll do reindexAll
                $eventResource->updateProcessEvents($this);
                $this->getIndexer()->reindexAll();
            }
            $this->unlock();

            if ($this->getMode() == self::MODE_MANUAL && $this->_eventRepository->hasUnprocessed($this)) {
                $this->_getResource()->updateStatus($this, self::STATUS_REQUIRE_REINDEX);
            } else {
                $this->_getResource()->endProcess($this);
            }
        } catch (Exception $e) {
            $this->unlock();
            $this->_getResource()->failProcess($this);
            throw $e;
        }
        $this->_eventManager->dispatch('after_reindex_process_' . $this->getIndexerCode());
    }

    /**
     * Reindex all data what this process responsible is
     * Check and using depends processes
     *
     * @return Magento_Index_Model_Process
     */
    public function reindexEverything()
    {
        if ($this->getData('runed_reindexall')) {
            return $this;
        }

        $this->setForcePartialReindex(
            $this->getStatus() == self::STATUS_PENDING && $this->_eventRepository->hasUnprocessed($this)
        );

        if ($this->getDepends()) {
            /** @var $indexer Magento_Index_Model_Indexer */
            $indexer = Mage::getSingleton('Magento_Index_Model_Indexer');
            foreach ($this->getDepends() as $code) {
                $process = $indexer->getProcessByCode($code);
                if ($process) {
                    $process->reindexEverything();
                }
            }
        }

        $this->setData('runed_reindexall', true);
        return $this->reindexAll();
    }

    /**
     * Process event with assigned indexer object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_Process
     */
    public function processEvent(Magento_Index_Model_Event $event)
    {
        if (!$this->matchEvent($event)) {
            return $this;
        }
        if ($this->getMode() == self::MODE_MANUAL) {
            $this->changeStatus(self::STATUS_REQUIRE_REINDEX);
            return $this;
        }

        // Commented  due to deadlock
        // @todo: Verify: It is required for partial update
        //$this->_getResource()->updateProcessStartDate($this);

        $this->_setEventNamespace($event);
        $isError = false;

        try {
            $this->getIndexer()->processEvent($event);
        } catch (Exception $e) {
            $isError = true;
        }
        $event->resetData();
        $this->_resetEventNamespace($event);
        //$this->_getResource()->updateProcessEndDate($this);
        $event->addProcessId($this->getId(), $isError ? self::EVENT_STATUS_ERROR : self::EVENT_STATUS_DONE);

        return $this;
    }

    /**
     * Get Indexer strategy object
     *
     * @return Magento_Index_Model_IndexerInterface
     */
    public function getIndexer()
    {
        if ($this->_indexer === null) {
            $code = $this->_getData('indexer_code');
            if (!$code) {
                Mage::throwException(__('Indexer code is not defined.'));
            }
            $this->_indexer = $this->_indexerFactory->create($code);
        }
        return $this->_indexer;
    }

    /**
     * Index pending events addressed to the process
     *
     * @param   null|string $entity
     * @param   null|string $type
     * @return  Magento_Index_Model_Process
     * @throws Exception
     */
    public function indexEvents($entity = null, $type = null)
    {
        /**
         * Check if process indexer can match entity code and action type
         */
        if ($entity !== null && $type !== null) {
            if (!$this->getIndexer()->matchEntityAndType($entity, $type)) {
                return $this;
            }
        }

        if ($this->getMode() == self::MODE_MANUAL) {
            return $this;
        }

        if ($this->isLocked()) {
            return $this;
        }

        $this->lock();
        try {
            /**
             * Prepare events collection
             */
            $eventsCollection = $this->_eventRepository->getUnprocessed($this);
            if ($entity !== null) {
                $eventsCollection->addEntityFilter($entity);
            }
            if ($type !== null) {
                $eventsCollection->addTypeFilter($type);
            }

            $this->_processEventsCollection($eventsCollection);
            $this->unlock();
        } catch (Exception $e) {
            $this->unlock();
            throw $e;
        }
        return $this;
    }

    /**
     * Process all events of the collection
     *
     * @param Magento_Index_Model_Resource_Event_Collection $eventsCollection
     * @param bool $skipUnmatched
     * @return Magento_Index_Model_Process
     */
    protected function _processEventsCollection(
        Magento_Index_Model_Resource_Event_Collection $eventsCollection,
        $skipUnmatched = true
    ) {
        // We can't reload the collection because of transaction
        /** @var $event Magento_Index_Model_Event */
        while (true == ($event = $eventsCollection->fetchItem())) {
            try {
                $this->processEvent($event);
                if (!$skipUnmatched) {
                    $eventProcessIds = $event->getProcessIds();
                    if (!isset($eventProcessIds[$this->getId()])) {
                        $event->addProcessId($this->getId(), null);
                    }
                }
            } catch (Exception $e) {
                $event->addProcessId($this->getId(), self::EVENT_STATUS_ERROR);
            }
            $event->save();
        }
        return $this;
    }

    /**
     * Update status process/event association
     *
     * @param   Magento_Index_Model_Event $event
     * @param   string $status
     * @return  Magento_Index_Model_Process
     */
    public function updateEventStatus(Magento_Index_Model_Event $event, $status)
    {
        $this->_getResource()->updateEventStatus($this->getId(), $event->getId(), $status);
        return $this;
    }

    /**
     * Get process file instance
     *
     * @return Magento_Index_Model_Process_File
     */
    protected function _getProcessFile()
    {
        if (!$this->_processFile) {
            $this->_processFile = $this->_lockStorage->getFile($this->getId());
        }
        return $this->_processFile;
    }

    /**
     * Lock process without blocking.
     * This method allow protect multiple process running and fast lock validation.
     *
     * @return Magento_Index_Model_Process
     */
    public function lock()
    {
        $this->_getProcessFile()->processLock();
        return $this;
    }

    /**
     * Lock and block process.
     * If new instance of the process will try validate locking state
     * script will wait until process will be unlocked
     *
     * @return Magento_Index_Model_Process
     */
    public function lockAndBlock()
    {
        $this->_getProcessFile()->processLock(false);
        return $this;
    }

    /**
     * Unlock process
     *
     * @return Magento_Index_Model_Process
     */
    public function unlock()
    {
        $this->_getProcessFile()->processUnlock();
        return $this;
    }

    /**
     * Check if process is locked by another user
     *
     * @param bool $needUnlock
     * @return bool
     */
    public function isLocked($needUnlock = false)
    {
        return $this->_getProcessFile()->isProcessLocked($needUnlock);
    }

    /**
     * Change process status
     *
     * @param string $status
     * @return Magento_Index_Model_Process
     */
    public function changeStatus($status)
    {
        $this->_eventManager->dispatch('index_process_change_status', array(
            'process' => $this,
            'status' => $status
        ));
        $this->_getResource()->updateStatus($this, $status);
        return $this;
    }

    /**
     * Get list of process mode options
     *
     * @return array
     */
    public function getModesOptions()
    {
        return array(
            self::MODE_REAL_TIME => __('Update on Save'),
            self::MODE_MANUAL => __('Manual Update')
        );
    }

    /**
     * Get list of process status options
     *
     * @return array
     */
    public function getStatusesOptions()
    {
        return array(
            self::STATUS_PENDING            => __('Ready'),
            self::STATUS_RUNNING            => __('Processing'),
            self::STATUS_REQUIRE_REINDEX    => __('Reindex Required'),
        );
    }

    /**
     * Get list of "Update Required" options
     *
     * @return array
     */
    public function getUpdateRequiredOptions()
    {
        return array(
            0 => __('No'),
            1 => __('Yes'),
        );
    }

    /**
     * Retrieve depend indexer codes
     *
     * @return array
     */
    public function getDepends()
    {
        $depends = $this->getData('depends');
        if (is_null($depends)) {
            $depends = array();
            $path = self::XML_PATH_INDEXER_DATA . '/' . $this->getIndexerCode();
            $node = Mage::getConfig()->getNode($path);
            if ($node) {
                $data = $node->asArray();
                if (isset($data['depends']) && is_array($data['depends'])) {
                    $depends = array_keys($data['depends']);
                }
            }

            $this->setData('depends', $depends);
        }

        return $depends;
    }

    /**
     * Process event with locks checking
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Index_Model_Process
     * @throws Exception
     */
    public function safeProcessEvent(Magento_Index_Model_Event $event)
    {
        if (!$this->matchEvent($event)) {
            return $this;
        }
        if ($this->isLocked()) {
            return $this;
        }
        $this->lock();
        try {
            $this->processEvent($event);
            $this->unlock();
        } catch (Exception $e) {
            $this->unlock();
            throw $e;
        }
        return $this;
    }
}
