<?php
/**
 * Solr client
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Client_Solr extends Enterprise_Search_Model_Client_Solr
{
    /**
     * Return replication details
     *
     * @return Apache_Solr_Response
     */
    public function getReplicationDetails()
    {
        return $this->_sendRawGet($this->_constructUrl('replication', array('command' => 'details')));
    }
}
