<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Model\Config\Source;

/**
 * Backups types' source model for system configuration
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Backup data
     *
     * @var \Magento\Backup\Helper\Data
     */
    protected $_backupData = null;

    /**
     * @param \Magento\Backup\Helper\Data $backupData
     */
    public function __construct(
        \Magento\Backup\Helper\Data $backupData
    ) {
        $this->_backupData = $backupData;
    }

    /**
     * Return possible options
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
