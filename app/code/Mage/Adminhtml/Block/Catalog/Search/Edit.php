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
 * Admin tag edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Search_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'catalog_search';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Mage_Catalog_Helper_Data')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('Mage_Catalog_Helper_Data')->__('Delete Search'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_catalog_search')->getId()) {
            return Mage::helper('Mage_Catalog_Helper_Data')->__("Edit Search '%1'", $this->escapeHtml(Mage::registry('current_catalog_search')->getQueryText()));
        }
        else {
            return Mage::helper('Mage_Catalog_Helper_Data')->__('New Search');
        }
    }

}
