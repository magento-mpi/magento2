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

        $this->_updateButton('save', 'label', __('Save Search'));
        $this->_updateButton('delete', 'label', __('Delete Search'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('current_catalog_search')->getId()) {
            return __("Edit Search '%s'", $this->escapeHtml(Mage::registry('current_catalog_search')->getQueryText()));
        }
        else {
            return __('New Search');
        }
    }

}
