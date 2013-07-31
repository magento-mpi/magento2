<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup types option array
 *
 * @category   Mage
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Model_Grid_Options implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * @var Mage_Backup_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backup_Helper_Data $backupHelper
     */
    public function __construct(Mage_Backup_Helper_Data $backupHelper)
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
