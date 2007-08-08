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

    public function __construct()
    {
        $this->_objectId = 'block_id';
        $this->_controller = 'cms_block';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Block'));
        $this->_updateButton('delete', 'label', __('Delete Block'));
        $this->_addButton('toggle', array(
            'label'     => __('Toggle Editor'),
            'onclick'   => 'toggleEditor()',
        ), 1);
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('cms_block')->getId()) {
            return __('Edit Block') . " '" . Mage::registry('current_promo_catalog_rule')->getTitle() . "'";
        }
        else {
            return __('New Block');
        }
    }

}
