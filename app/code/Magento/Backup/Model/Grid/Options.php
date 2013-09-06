<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup types option array
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Model_Grid_Options implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * @var Magento_Backup_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Backup_Helper_Data $backupHelper
     */
    public function __construct(Magento_Backup_Helper_Data $backupHelper)
    {
        $this->_helper = $backupHelper;
    }

    /**
     * Return backup types array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_helper->getBackupTypes();
    }
}
