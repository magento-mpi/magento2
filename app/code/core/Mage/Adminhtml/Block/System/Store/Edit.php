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

class Mage_Adminhtml_Block_System_Store_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'store';
        $this->_controller = 'system_store';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save Store'));
        $this->_updateButton('delete', 'label', __('Delete Store'));
    }
    
    public function getBackUrl()
    {
        return Mage::getUrl('*/system_config/edit', array('store'=>Mage::registry('admin_current_store')->getCode()));
    }

    public function getHeaderText()
    {
        if (Mage::registry('admin_current_store')->getId()) {
            return __('Edit Store') . " '" . Mage::registry('admin_current_store')->getName() . "'";
        }
        else {
            return __('New Store');
        }
    }

}
