<?php
/**
 * Solr balancer (php extension)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Client_Balancer_PhpExtension extends Saas_Search_Model_Client_BalancerAbstract
{
    /**
     * Initialize Solr client
     *
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Core_Model_Logger $logger
     * @param Enterprise_Search_Model_Client_SolrClient_Factory $_solrClientFactory
     * @param array $options
     */
    public function __construct(
        Mage_Core_Model_Registry $registry,
        Mage_Core_Model_Logger $logger,
        Enterprise_Search_Model_Client_SolrClient_Factory $_solrClientFactory,
        array $options = array()
    ) {
        $this->_solrClientFactory = $_solrClientFactory;
        parent::__construct($registry, $logger, $options);
    }

    /**
     * Add an array of Solr Documents to the index all at once
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  array $documents Should be an array of Apache_Solr_Document instances
     * @param  boolean $allowDups
     * @param  boolean $overwritePending
     * @param  boolean $overwriteCommitted
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function addDocuments($documents, $allowDups = false, $overwritePending = true, $overwriteCommitted = true)
    {
        $service = $this->_selectWriteService();
        do {
            try {
                return $service->addDocuments($documents, $allowDups, $overwritePending);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    $this->_log->logException($e);
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Create a delete document based on a multiple queries and submit it
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
                return $service->deleteByQueries($rawQueries);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    // throw $e;
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Alias to Apache_Solr_Service::deleteByMultipleIds() method
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
                return $service->deleteByIds($ids);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    // throw $e;
                }
            }
            $service = $this->_selectWriteService(true);
        } while ($service);
        return false;
    }

    /**
     * Check current read service
     *
     * @return boolean
     */
    public function ping()
    {
        $service = $this->_selectReadService();
        do {
            try {
                return $service->ping();
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    // throw $e;
                }
            }
            $service = $this->_selectReadService(true);
        } while ($service);
        return false;
    }

    /**
     * Change solr servlet
     *
     * @param string $type
     * @param string $path
     */
    public function setServlet($type, $path)
    {
        $this->_reconnect();
        $service = $this->_selectReadService();
        $service->setServlet($type, $path);
        $this->_selectWriteService()->setServlet($type, $path);
    }

    /**
     * Simple solr query using balancer
     *
     * @param  mixed $query
     * @return object
     */
    public function query($query)
    {
        $service = $this->_selectReadService();

        do {
            try {
                return $service->query($query);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    //throw $e;
                }
            }
            $service = $this->_selectReadService(true);
        } while ($service);
        return false;
    }

    /**
     * Retrieve solr client object
     *
     * @param  array $config
     * @return SolrClient
     */
    protected function _getService($config)
    {
        return $this->_solrClientFactory->createClient($config);
    }

    /**
     * Reconnect to current service
     *
     * Hard code to prevent Solr bug:
     * Bug #17009 Creating two SolrQuery objects leads to wrong query value
     * @see http://pecl.php.net/bugs/bug.php?id=17009&edit=1
     * @see http://svn.php.net/viewvc?view=revision&revision=293379
     */
    protected function _reconnect()
    {
        $service = $this->_selectReadService();
        $options = $service->getOptions();
        $this->_readableServices[$this->_currentReadService] = $this->_getService(
            array (
                'port'     => $options['port'],
                'timeout'  => $options['timeout'],
                'path'     => $options['path'],
                'hostname' => $options['hostname'],
            )
        );
    }

    /**
     * Retrieve index version of service
     *
     * todo Implement this functionality
     *
     * @throws Exception
     * @return bool|string
     */
    public function getIndexVersion()
    {
        return false;
    }
}
