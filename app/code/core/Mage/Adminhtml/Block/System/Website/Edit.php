<?php
/**
 * Admin tag edit block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_System_Website_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'website';
        $this->_controller = 'system_website';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Website'));
        $this->_updateButton('delete', 'label', __('Delete Website'));
    }
    
    public function getBackUrl()
    {
        return Mage::getUrl('*/system_config/edit', array('website'=>Mage::registry('admin_current_website')->getCode()));
    }

    public function getHeaderText()
    {
        if (Mage::registry('admin_current_website')->getId()) {
            return __('Edit Website') . " '" . Mage::registry('admin_current_website')->getName() . "'";
        }
        else {
            return __('New Website');
        }
    }

}
