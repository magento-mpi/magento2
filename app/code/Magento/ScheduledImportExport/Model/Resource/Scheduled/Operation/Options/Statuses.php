<?php
/**
 * Import/Export Schedule statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation\Options;

class Statuses
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data
     */
    protected $_modelData;

    /**
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $model
     */
    public function __construct(\Magento\ScheduledImportExport\Model\Scheduled\Operation\Data $model)
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
