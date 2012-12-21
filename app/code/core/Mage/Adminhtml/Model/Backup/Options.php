<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_Backup_Options implements Mage_Core_Model_Option_ArrayInterface
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
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_helper->getBackupTypes();
    }
}
