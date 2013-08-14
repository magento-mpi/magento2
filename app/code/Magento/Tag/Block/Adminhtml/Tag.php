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
 * Adminhtml all tags
 *
 * @category   Mage
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Adminhtml_Tag extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_tag';
        $this->_headerText = Mage::helper('Magento_Tag_Helper_Data')->__('Manage Tags');
        $this->_addButtonLabel = Mage::helper('Magento_Tag_Helper_Data')->__('Add New Tag');
        parent::_construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-tag';
    }
}
