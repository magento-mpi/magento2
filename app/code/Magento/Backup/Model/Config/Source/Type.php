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
namespace Magento\Backup\Model\Config\Source;

class Type implements \Magento\Core\Model\Option\ArrayInterface
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
