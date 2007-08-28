<?php
/**
 * Adminhtml permissions user edit page
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'user_id';
        $this->_controller = 'permissions_user';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Save User'));
        $this->_updateButton('delete', 'label', __('Delete User'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('permissions_user')->getId()) {
            return __('Edit User') . " '" . Mage::registry('permissions_user')->getUsername() . "'";
        }
        else {
            return __('New User');
        }
    }

}
