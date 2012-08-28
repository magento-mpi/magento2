<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customers tag blocks content block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Adminhtml_Report_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'Mage_Tag';
        $this->_controller = 'adminhtml_report_customer';
        $this->_headerText = Mage::helper('Mage_Tag_Helper_Data')->__('Customers Tags');
        parent::__construct();
        $this->_removeButton('add');
    }

}
