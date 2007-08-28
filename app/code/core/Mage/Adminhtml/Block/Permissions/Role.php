<?php
/**
 * Adminhtml permissioms role block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Permissions_Role extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'permissions_role';
        $this->_headerText = __('Roles');
        $this->_addButtonLabel = __('Add New Role');
        parent::__construct();
    }

}
