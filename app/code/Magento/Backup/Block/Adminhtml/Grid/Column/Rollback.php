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
 * Grid column block that is displayed only if rollback allowed
 *
 * @category   Magento
 * @package    Magento_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backup_Block_Adminhtml_Grid_Column_Rollback extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * Backup data
     *
     * @var Magento_Backup_Helper_Data
     */
    protected $_backupData = null;

    /**
     * @param Magento_Backup_Helper_Data $backupData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Backup_Helper_Data $backupData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_backupData = $backupData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Check permission for rollback
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->_backupData->isRollbackAllowed();
    }
}
