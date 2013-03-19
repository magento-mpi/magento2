<?php
/**
 * Solr search engine adapter that perform raw queries to Solr server based on Conduit solr client library
 * and basic solr adapter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Adapter_HttpStream extends Enterprise_Search_Model_Adapter_HttpStream
{
    /**
     * Connect to Solr Client by specified options that will be merged with default
     *
     * @param array $options
     * @return Saas_Search_Model_Client_Balancer_HttpStream
     */
    protected function _connect($options = array())
    {
        /** @var $helper Saas_Search_Helper_Data */
        $helper = Mage::helper('Saas_Search_Helper_Data');
        $def_options = $helper->getSolrServers();
        $options = array_merge($def_options, $options);

        try {
            $this->_client = Mage::getModel('Saas_Search_Model_Client_Balancer_HttpStream',
                array('options' => $options)
            );
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this->_client;
    }
}
