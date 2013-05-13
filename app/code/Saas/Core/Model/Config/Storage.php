<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Core_Model_Config_Storage implements Mage_Core_Model_Config_StorageInterface
{
    /**
     * Cache invalidation flag
     *
     * @var bool
     */
    protected $_cacheInvalidated = false;

    /**
     * Cache storage object
     *
     * @var Mage_Core_Model_Config_Cache
     */
    protected $_cache;

    /**
     * Resource configuration
     *
     * @var Mage_Core_Model_Config_Resource
     */
    protected $_resourcesConfig;

    /**
     * @var Enterprise_Queue_Model_Event_HandlerInterface
     */
    protected $_queueHandler;

    /**
     * Varien event factory
     *
     * @var Varien_EventFactory
     */
    protected $_eventFactory;

    /**
     * @param Mage_Core_Model_Config_Cache $cache
     * @param Mage_Core_Model_Config_Resource $resourcesConfig
     * @param Enterprise_Queue_Model_Event_HandlerInterface $queueHandler
     * @param Varien_EventFactory $eventFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Cache $cache,
        Mage_Core_Model_Config_Resource $resourcesConfig,
        Enterprise_Queue_Model_Event_HandlerInterface $queueHandler,
        Varien_EventFactory $eventFactory
    ) {
        $this->_cache = $cache;
        $this->_resourcesConfig = $resourcesConfig;
        $this->_queueHandler = $queueHandler;
        $this->_eventFactory = $eventFactory;
    }


    /**
     * Retrieve application configuration
     *
     * @throws Saas_Core_Model_Config_Exception
     * @return Mage_Core_Model_ConfigInterface
     */
    public function getConfiguration()
    {
        $config = $this->_cache->load();
        if (false === $config || $this->_cacheInvalidated) {
            $event = $this->_eventFactory->create();
            $eventName = 'application_process_reinit_config';
            $event->setName($eventName);
            $data = array(
                'observer'      => array('event' => $event),
                'configuration' => array(
                    'type'   => '',
                    'model'  => 'Saas_Queue_Model_Observer_Config',
                    'method' => 'processReinitConfig',
                    'config' => array(
                        'params'       => array('task_name' => 'regenerate_config'),
                        'asynchronous' => true,
                        'priority'     => 10,
                        'class'        => 'Saas_Queue_Model_Observer_Config',
                        'method'       => 'processReinitConfig'
                    )
                )
            );
            $priority = 10;
            $this->_queueHandler->addTask($eventName, $data, $priority);
            $this->_cacheInvalidated = false;
        }
        if (false === $config) {
            throw new Saas_Core_Model_Config_Exception();
        } else {
            /*
             * Update resource configuration when total configuration is loaded.
             * Required until resource model is refactored.
             */
            $this->_resourcesConfig->setConfig($config);
        }

        return $config;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_cacheInvalidated = true;
    }
}
