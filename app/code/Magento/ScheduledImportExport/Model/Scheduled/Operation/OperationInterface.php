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
namespace Magento\ScheduledImportExport\Model\Scheduled\Operation;

interface OperationInterface
{
    /**
     * Run operation through cron
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return bool
     */
    function runSchedule(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation);


    /**
     * Initialize operation model from scheduled operation
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return object operation instance
     */
    function initialize(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation);

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
