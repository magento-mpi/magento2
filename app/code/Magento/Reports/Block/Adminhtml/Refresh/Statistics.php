<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Report Refresh statistic container
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Block_Adminhtml_Refresh_Statistics extends Magento_Backend_Block_Widget_Grid_Container
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
