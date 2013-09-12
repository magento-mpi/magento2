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
     * Check permission for rollback
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->helper('Magento_Backup_Helper_Data')->isRollbackAllowed();
    }
}
