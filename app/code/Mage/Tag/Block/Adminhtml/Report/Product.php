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
 * Adminhtml tag report product blocks content block
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Tag_Block_Adminhtml_Report_Product extends Mage_Backend_Block_Widget_Grid_Container
{
    public function _construct()
    {
        $this->_blockGroup = 'Mage_Tag';
        $this->_controller = 'adminhtml_report_product';
        $this->_headerText = __('Products Tags');
        parent::_construct();
        $this->_removeButton('add');
    }

}
