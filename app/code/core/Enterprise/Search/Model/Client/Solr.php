<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Solr client
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

include_once 'Solr/Service.php';

class Enterprise_Search_Model_Client_Solr extends Apache_Solr_Service
{
    /**
     * Initialize Solr Client
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        return parent::__construct($options['hostname'], $options['port'], '/' . $options['path'] . '/');
    }

    /**
     * Send an rollback command.
     *
     * @param float $timeout Maximum expected duration of the commit operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function rollback($timeout = 3600)
    {
        $rawPost = '<rollback/>';
        return $this->_sendRawPost($this->_updateUrl, $rawPost, $timeout);
    }

    /**
     * Create a delete document based on a multiple queries and submit it
     *
     * @param array $rawQueries Expected to be utf-8 encoded
     * @param boolean $fromPending
     * @param boolean $fromCommitted
     * @param float $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByQueries($rawQueries, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $pendingValue = $fromPending ? 'true' : 'false';
        $committedValue = $fromCommitted ? 'true' : 'false';

        $rawPost = '<delete fromPending="' . $pendingValue . '" fromCommitted="' . $committedValue . '">';

        foreach ($rawQueries as $query)
        {
            //escape special xml characters
            $query = htmlspecialchars($query, ENT_NOQUOTES, 'UTF-8');

            $rawPost .= '<query>' . $query . '</query>';
        }

        $rawPost .= '</delete>';

        return $this->delete($rawPost, $timeout);
    }

    /**
     * Alias to Apache_Solr_Service::deleteByMultipleIds() method
     *
     * @param array $ids Expected to be utf-8 encoded strings
     * @param boolean $fromPending
     * @param boolean $fromCommitted
     * @param float $timeout Maximum expected duration of the delete operation on the server (otherwise, will throw a communication exception)
     * @return Apache_Solr_Response
     *
     * @throws Exception If an error occurs during the service call
     */
    public function deleteByIds($ids, $fromPending = true, $fromCommitted = true, $timeout = 3600)
    {
        $this->deleteByMultipleIds($ids, $fromPending, $fromCommitted, $timeout);
    }
}