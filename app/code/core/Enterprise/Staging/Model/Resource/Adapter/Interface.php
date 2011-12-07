<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging Resource Adapter Interface
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Enterprise_Staging_Model_Resource_Adapter_Interface
{
    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function checkfrontendRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;

    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function createRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;

    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function updateRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;

    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function backupRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;

    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function mergeRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;

    /**
     * Enter description here ...
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param unknown_type $event
     */
    public function rollbackRun(Enterprise_Staging_Model_Staging $staging, $event = null)
;
}
