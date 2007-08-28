<?php
/**
 * Adminhtml permissions user block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_User extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'permissions_user';
        $this->_headerText = __('Users');
        $this->_addButtonLabel = __('Add New User');
        parent::__construct();
    }

}
