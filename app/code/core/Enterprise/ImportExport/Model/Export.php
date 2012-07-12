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
 * Export model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getOperationType() getOperationType()
 */
class Enterprise_ImportExport_Model_Export extends Mage_ImportExport_Model_Export
    implements Enterprise_ImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * Date model instance
     *
     * @var Mage_Core_Model_Date
     */
    protected $_dateModel;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        $this->_dateModel = isset($data['date_model']) ? $data['date_model'] : Mage::getModel('Mage_Core_Model_Date');
    }

    /**
     * Date model instance getter
     *
     * @return Mage_Core_Model_Date
     */
    public function getDateModel()
    {
        return $this->_dateModel;
    }

    /**
     * Run export through cron
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    public function runSchedule(Enterprise_ImportExport_Model_Scheduled_Operation $operation)
    {
        $data = $this->export();
        $result = $operation->saveFileSource($this, $data);

        return (bool)$result;
    }

    /**
     * Initialize export instance from scheduled operation
     *
     * @param Enterprise_ImportExport_Model_Scheduled_Operation $operation
     * @return Enterprise_ImportExport_Model_Export
     */
    public function initialize(Enterprise_ImportExport_Model_Scheduled_Operation $operation)
    {
        $fileInfo  = $operation->getFileInfo();
        $attributes = $operation->getEntityAttributes();
        $data = array(
            'entity'                 => $operation->getEntityType(),
            'entity_subtype'         => $operation->getEntitySubtype(),
            'file_format'            => $fileInfo['file_format'],
            'export_filter'          => $attributes['export_filter'],
            'operation_type'         => $operation->getOperationType(),
            'run_at'                 => $operation->getStartTime(),
            'scheduled_operation_id' => $operation->getId()
        );
        if (isset($attributes['skip_attr'])) {
            $data['skip_attr'] = $attributes['skip_attr'];
        }
        $this->setData($data);
        return $this;
    }

    /**
     * Get file name for scheduled running
     *
     * @return string file name without extension
     */
    public function getScheduledFileName()
    {
        if ($this->getEntitySubtype()) {
            $suffix = $this->getEntitySubtype();
        } else {
            $suffix = $this->getEntity();
        }

        return $this->getDateModel()->date('Y-m-d_H-i-s') . '_' . $this->getOperationType()
            . '_' . $suffix;
    }
}
