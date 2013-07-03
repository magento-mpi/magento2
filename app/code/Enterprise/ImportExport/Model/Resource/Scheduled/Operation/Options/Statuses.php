<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_ImportExport_Model_Resource_Scheduled_Operation_Options_Statuses
    implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Enterprise_ImportExport_Model_Scheduled_Operation_Data
     */
    protected $_modelData;

    /**
     * @param Enterprise_ImportExport_Model_Scheduled_Operation_Data $model
     */
    public function __construct(Enterprise_ImportExport_Model_Scheduled_Operation_Data $model)
    {
        $this->_modelData = $model;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return  $this->_modelData->getStatusesOptionArray();
    }
}