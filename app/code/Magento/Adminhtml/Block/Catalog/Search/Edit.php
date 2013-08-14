<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tag edit block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Search_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'catalog_search';

        parent::_construct();

        $this->_updateButton('save', 'label', Mage::helper('Magento_Catalog_Helper_Data')->__('Save Search'));
        $this->_updateButton('delete', 'label', Mage::helper('Magento_Catalog_Helper_Data')->__('Delete Search'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_catalog_search')->getId()) {
            return Mage::helper('Magento_Catalog_Helper_Data')->__("Edit Search '%s'", $this->escapeHtml(Mage::registry('current_catalog_search')->getQueryText()));
        }
        else {
            return Mage::helper('Magento_Catalog_Helper_Data')->__('New Search');
        }
    }

}
