<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Scheduled operation interface
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Enterprise_ImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * Run operation through cron
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    function runSchedule(Enterprise_ImportExport_Model_Scheduled_Operation $operation);


    /**
     * Initialize operation model from scheduled operation
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return object operation instance
     */
    function initialize(Enterprise_ImportExport_Model_Scheduled_Operation $operation);

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
