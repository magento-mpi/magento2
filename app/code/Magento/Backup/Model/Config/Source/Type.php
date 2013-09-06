<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backups types' source model for system configuration
 *
 * @category   Magento
 * @package    \Magento\Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Model_Config_Source_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * return possible options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $backupTypes = array();
        foreach(Mage::helper('Magento_Backup_Helper_Data')->getBackupTypes() as $type => $label) {
            $backupTypes[] = array(
                'label' => $label,
                'value' => $type,
            );
        }
        return $backupTypes;
    }
}
