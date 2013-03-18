<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Solr balancer (http-stream)
 *
 * @method Saas_Search_Model_Client_Solr _selectReadService()
 *
 * @category   Saas
 * @package    Saas_Search
 */
class Saas_Search_Model_Client_Balancer_HttpStream extends Saas_Search_Model_Client_Balancer_Abstract
{
    /**
     * Retrieve search suggestions using balancer
     *
     * @param  string $query
     * @param  array $_params
     * @throws Exception
     * @return Apache_Solr_Response|bool
     */
    public function searchSuggestions($query, $_params)
    {
        $service = $this->_selectReadService();
        do {
            try {
                return $service->searchSuggestions($query, $_params);
            } catch (Exception $e) {
                if ($e->getCode() != 0) {
                    throw $e;
                }
            }
            $service = $this->_selectReadService(true);
        } while ($service);

        return false;
    }

    /**
     * Retrieve solr client object
     *
     * @param  array $configs
     * @return Saas_Search_Model_Client_Solr
     */
    protected function _getService($configs)
    {
        return Mage::getModel('Saas_Search_Model_Client_Solr', array('options' => $configs));
    }

    /**
     * Get replication details
     *
     * @return Apache_Solr_Response|bool
     * @throws Exception
     */
    public function getReplicationDetails()
    {
        $service = $this->_selectReadService();
        do {
            try {
                $response = $service->getReplicationDetails();
                if ($response) {
                    return $response;
                }
            } catch (Exception $e) {
                throw $e;
            }
            $service = $this->_selectReadService(true);
        } while ($service);
        return false;
    }

    /**
     * Retrieve index version of service
     *
     * @throws Exception
     * @return bool|string
     */
    public function getIndexVersion()
    {
        $response = $this->getReplicationDetails();
        if ($response->getHttpStatus() == 200) {
            $xml = simplexml_load_string($response->getRawResponse());
            $indexVersions = $xml->xpath("//long[@name='indexVersion']");
            if (isset($indexVersions[0])) {
                return (string)$indexVersions[0];
            }
        }
        return false;
    }
}
