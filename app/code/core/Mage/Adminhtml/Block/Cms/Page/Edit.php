<?php
/**
 * Admin CMS page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Cms_Page_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'page_id';
        $this->_controller = 'cms_page';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Page'));
        $this->_updateButton('delete', 'label', __('Delete Page'));

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('cms_page')->getId()) {
            return __('Edit Page') . " '" . Mage::registry('cms_page')->getTitle() . "'";
        }
        else {
            return __('New Page');
        }
    }

}
