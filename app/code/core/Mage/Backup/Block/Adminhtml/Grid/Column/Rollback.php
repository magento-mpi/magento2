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
     * Backup grid item renderer
     *
     * @category   Mage
     * @package    Mage_Backup
     * @author     Magento Core Team <core@magentocommerce.com>
     */
class Mage_Backup_Block_Adminhtml_Grid_Column_Rollback extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * Get header css class name
     *
     * @return string
     */
    public function isDisplayed()
    {
        return $this->helper('Mage_Backup_Helper_Data')->isRollbackAllowed();
    }
}