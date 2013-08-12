<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backup
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column block that is displayed only if rollback allowed
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup_Block_Adminhtml_Grid_Column_Rollback extends Magento_Backend_Block_Widget_Grid_Column
{
    /**
     * Check permission for rollback
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return $this->helper('Mage_Backup_Helper_Data')->isRollbackAllowed();
    }
}