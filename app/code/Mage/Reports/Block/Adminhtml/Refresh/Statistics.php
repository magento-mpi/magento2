<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Refresh statistic container
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Block_Adminhtml_Refresh_Statistics extends Mage_Backend_Block_Widget_Grid_Container
{
    /*
     * Modify Header and remove button "Add"
     */
    protected function _construct()
    {
        $this->_controller = 'report_refresh_statistics';
        $this->_headerText = __('Refresh Statistics');
        parent::_construct();
        $this->_removeButton('add');
    }
}
