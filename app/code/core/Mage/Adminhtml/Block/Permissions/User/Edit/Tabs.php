<?php
/**
 * Admin page left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_User_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('User Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => __('User Info'),
            'title'     => __('User Info'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_user_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('roles_section', array(
            'label'     => __('Roles'),
            'title'     => __('Roles'),
            'content'   => $this->getLayout()->createBlock('adminhtml/permissions_user_edit_tab_roles')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}
