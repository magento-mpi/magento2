<?php
/**
 * Admin CMS block edit
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    protected function _init()
    {
        parent::_init();
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';
        $this->_updateButton('save', 'label', __('Save Block'));
        $this->_updateButton('delete', 'label', __('Delete Block'));
        $this->_addButton('toggle', array(
            'label'     => __('Toggle Editor'),
            'onclick'   => 'toggleEditor()',
        ));
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }
        ";
        return $this;
    }

    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return __('Edit Block') . " '" . Mage::registry('cms_block')->getTitle() . "'";
        }
        else {
            return __('New Block');
        }
    }

}
