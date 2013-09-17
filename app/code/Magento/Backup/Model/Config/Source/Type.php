<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backups types' source model for system configuration
 *
 * @category   Magento
 * @package    Magento_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Model_Config_Source_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Backup data
     *
     * @var Magento_Backup_Helper_Data
     */
    protected $_backupData = null;

    /**
     * @param Magento_Backup_Helper_Data $backupData
     */
    public function __construct(
        Magento_Backup_Helper_Data $backupData
    ) {
        $this->_backupData = $backupData;
    }

    /**
     * return possible options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $backupTypes = array();
        foreach($this->_backupData->getBackupTypes() as $type => $label) {
            $backupTypes[] = array(
                'label' => $label,
                'value' => $type,
            );
        }
        return $backupTypes;
    }
}
