<?php
/**
 * Import/Export Schedule result option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ScheduledImportExport_Model_Resource_Scheduled_Operation_Options_Result
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_ScheduledImportExport_Model_Scheduled_Operation_Data
     */
    protected $_modelData;

    /**
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $model
     */
    public function __construct(Magento_ScheduledImportExport_Model_Scheduled_Operation_Data $model)
    {
        $this->_modelData = $model;
    }

    /**
     * Return result array
     * @return array
     */
    public function toOptionArray()
    {
        return  $this->_modelData->getResultOptionArray();
    }
}
