<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers tag blocks content block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Customer extends Magento_Backend_Block_Widget_Grid_Container
{
    public function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_report_customer';
        $this->_headerText = __('Customers Tags');
        parent::_construct();
        $this->_removeButton('add');
    }
}
