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
 * Solr client
 *
 * @category   Saas
 * @package    Saas_Search
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
