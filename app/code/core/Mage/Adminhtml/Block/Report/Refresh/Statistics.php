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
 * Adminhtml report filter form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Refresh_Statistics extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'report_refresh_statistics';
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Refresh Statistics');
        parent::_construct();
        $this->_removeButton('add');
    }
}
