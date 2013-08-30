<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation interface
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * Run operation through cron
     *
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    function runSchedule(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation);


    /**
     * Initialize operation model from scheduled operation
     *
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return object operation instance
     */
    function initialize(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation);

    /**
     * Log debug data to file.
     *
     * @param mixed $debugData
     * @return object
     */
    function addLogComment($debugData);

    /**
     * Return human readable debug trace.
     *
     * @return array
     */
    function getFormatedLogTrace();
}
