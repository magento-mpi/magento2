<?php
/**
 * Abstract solr load balancer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Saas_Search_Model_Client_BalancerAbstract extends Apache_Solr_Service_Balancer
{
    /**
     * Registry model
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Logger
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_log;

    /**
     * Solr client factory
     *
     * @var Enterprise_Search_Model_Client_FactoryInterface
     */
    protected $_solrClientFactory;

    /**
     * Retrieve solr client object
     *
     * @abstract
     * @param $configs
     */
    abstract protected function _getService($configs);

    /**
     * Receive index version
     *
     * @abstract
     */
    abstract public function getIndexVersion();

    /**
     * Initialize Solr client
     *
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Logger $logger
     * @param array $options
     * @throws InvalidArgumentException
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Logger $logger,
        array $options = array()
    ) {
        $this->_registryManager = $registry;
        $this->_log = $logger;
        $_optionsNames = array(
            'masters',
            'slaves',
        );
        if (!sizeof(array_intersect($_optionsNames, array_keys($options)))) {
            throw new InvalidArgumentException('Missing required keys in the options.');
        }
        if (isset($options['timeout'])) {
            ini_set('default_socket_timeout', $options['timeout']);
        }
        parent::__construct($options['slaves'], $options['masters']);
        return $this;
    }

    /**
     * Send an rollback command.
     *
     * @param  int|float $timeout Maximum expected duration of the commit operation on the server
     *  (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response|bool
     *
     * @throws Exception If an error occurs during the service call
     */
    public function rollback($timeout = 3600)
    {
        $service = $this->_selectWriteService();
        do {
            try {
                return $service->rollback($timeout);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    throw $e;
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Create a delete document based on a multiple queries and submit it
     *
     * @param  array $rawQueries Expected to be utf-8 encoded
     * @param  boolean $fromPending
     * @param  boolean $fromCommitted
     * @param  int|float $timeout Maximum expected duration of the delete operation on the server
     *  (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByQueries($rawQueries, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $service = $this->_selectWriteService();
        do {
            try {
                return $service->deleteByQueries($rawQueries, $fromPending, $fromCommitted, $timeout);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    throw $e;
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Alias to Apache_Solr_Service::deleteByMultipleIds() method
     *
     * @param  array $ids Expected to be utf-8 encoded strings
     * @param  boolean $fromPending
     * @param  boolean $fromCommitted
     * @param  int|float $timeout Maximum expected duration of the delete operation on the server
     *  (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByIds($ids, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $service = $this->_selectWriteService();
        do {
            try {
                return $service->deleteByMultipleIds($ids, $fromPending, $fromCommitted, $timeout);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    throw $e;
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Setter for solr server username
     *
     * @param string $username
     */
    public function setUserLogin($username)
    {
        $this->_selectReadService()->setUserLogin($username);
    }

    /**
     * Getter of solr server username
     *
     * @return string
     */
    public function getUserLogin()
    {
        $this->_selectReadService()->getUserLogin();
    }

    /**
     * Setter for solr server password
     *
     * @param string $username
     */
    public function setPassword($username)
    {
        $this->_selectReadService()->setPassword($username);
    }

    /**
     * Getter of solr server password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_selectReadService()->getPassword();
    }

    /**
     * Check read services and return available
     *
     * @return string|false
     */
    public function ping()
    {
        $service = $this->_selectReadService();
        do {
            try {
                $ping = $service->ping();
                if ($ping) {
                    return $ping;
                }
            } catch (Exception $e) {
                if ($e->getcode() != 0) {
                    $this->_log->logException($e);
                }
            }
            $service = $this->_selectReadService(true);
        } while ($service);
        return false;
    }

    /**
     * Iterate through available read services and select the first with a ping
     * that satisfies configured timeout restrictions (or the default)
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param  bool $forceSelect
     * @return Saas_Search_Model_Client_Solr
     *
     * @throws Exception If there are no read services that meet requirements
     */
    protected function _selectReadService($forceSelect = false)
    {
        if (!$this->_currentReadService
            || !isset($this->_readableServices[$this->_currentReadService])
            || $forceSelect
        ) {
            if ($this->_currentReadService
                && isset($this->_readableServices[$this->_currentReadService])
                && $forceSelect
            ) {
                // we probably had a communication error, ping the current read service, remove it if it times out
                try {
                    $server = $this->_readableServices[$this->_currentReadService];
                    if ($server->ping($this->_readPingTimeout) === false) {
                        throw new Exception();
                    }
                } catch (Exception $e) {
                    $this->removeReadService($this->_currentReadService);
                }
            }

            if (count($this->_readableServices)) {
                // select one of the read services at random
                $ids = array_keys($this->_readableServices);
                $serviceId = $ids[rand(0, count($ids) - 1)];
                $service = $this->_readableServices[$serviceId];
                if (is_array($service)) {
                    //convert the array definition to a client object
                    $service['hostname'] = $service['host'];
                    unset($service['host']);
                    $service = $this->_getService($service);
                    $this->_readableServices[$serviceId] = $service;
                }
                $this->_currentReadService = $serviceId;
            } else {
                throw new Exception('No read services were available');
            }
        }
        return $this->_readableServices[$this->_currentReadService];
    }

    /**
     * Iterate through available write services and select the first with a ping
     * that satisfies configured timeout restrictions (or the default)
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param  bool $forceSelect
     * @return Saas_Search_Model_Client_Solr
     *
     * @throws Exception If there are no write services that meet requirements
     */
    protected function _selectWriteService($forceSelect = false)
    {
        if ($this->_useBackoff) {
            return $this->_selectWriteServiceSafe($forceSelect);
        }
        if (!$this->_currentWriteService
            || !isset($this->_writeableServices[$this->_currentWriteService])
            || $forceSelect
        ) {
            if ($this->_currentWriteService
                && isset($this->_writeableServices[$this->_currentWriteService])
                && $forceSelect
            ) {
                // we probably had a communication error, ping the current read service, remove it if it times out
                try {
                    $server = $this->_writeableServices[$this->_currentWriteService];
                    if ($server->ping($this->_writePingTimeout) === false) {
                        throw new Exception();
                    }
                } catch (Exception $e) {
                    $this->removeWriteService($this->_currentWriteService);
                }
            }
            if (count($this->_writeableServices)) {
                // select one of the read services at random
                $ids = array_keys($this->_writeableServices);
                $serviceId = $ids[rand(0, count($ids) - 1)];
                $service = $this->_writeableServices[$serviceId];
                if (is_array($service)) {
                    //convert the array definition to a client object
                    $service['hostname'] = $service['host'];
                    unset($service['host']);
                    $service = $this->_getService($service);
                    $this->_writeableServices[$serviceId] = $service;
                }
                $this->_currentWriteService = $serviceId;
            } else {
                throw new Exception('No write services were available');
            }
        }
        return $this->_writeableServices[$this->_currentWriteService];
    }

    /**
     * Save version of index in registry
     *
     * @return Saas_Search_Model_Client_BalancerAbstract
     */
    protected function _rememberIndexVersion()
    {
        if (!$this->_registryManager->registry('search_engine_index_version')) {
            try {
                $indexVersion = $this->getIndexVersion();
                $this->_registryManager->register('search_engine_index_version', $indexVersion);
            } catch (Exception $e) {
                $this->_log->logException($e);
            }
        }
        return $this;
    }

    /**
     * Simple search interface with getting index version
     *
     * @param  $query
     * @param  int $offset
     * @param  int $limit
     * @param  array $params
     * @param string $method
     * @return Apache_Solr_Response|false
     * @throws Exception
     */
    public function search(
        $query,
        $offset = 0,
        $limit = 10,
        $params = array(),
        $method = Apache_Solr_Service::METHOD_GET
    ) {
        $this->_rememberIndexVersion();
        $service = $this->_selectReadService();
        do {
            try {
                return $service->search($query, $offset, $limit, $params, $method);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    throw $e;
                }
            }
            $service = $this->_selectReadService(true);
        } while ($service);
        return false;
    }
}
