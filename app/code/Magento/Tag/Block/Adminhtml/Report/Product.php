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
 * Adminhtml tag report product blocks content block
 *
 * @category   Mage
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Product extends Mage_Backend_Block_Widget_Grid_Container
{
    public function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_report_product';
        $this->_headerText = Mage::helper('Magento_Tag_Helper_Data')->__('Products Tags');
        parent::_construct();
        $this->_removeButton('add');
    }

}
