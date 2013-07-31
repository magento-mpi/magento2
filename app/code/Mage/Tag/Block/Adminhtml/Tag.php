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
 * Adminhtml all tags
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Adminhtml_Tag extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_blockGroup = 'Mage_Tag';
        $this->_controller = 'adminhtml_tag';
        $this->_headerText = Mage::helper('Mage_Tag_Helper_Data')->__('Manage Tags');
        $this->_addButtonLabel = Mage::helper('Mage_Tag_Helper_Data')->__('Add New Tag');
        parent::_construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-tag';
    }
}
