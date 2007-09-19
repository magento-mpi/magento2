<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Roles extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setTitle(__('User Roles Information'));
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('permissions/role_collection');
        //$collection->setUserFilter(Mage::registry('permissions_user')->getUserId());
        $collection->setRolesFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

    	$this->addColumn('roles', array(
            'header_css_class' => 'a-center',
            'header'    => __('Assigned Role'),
            'type'      => 'radio',
            'name'      => 'role',
            'values'    => $this->_getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'role_id'
        ));

        /*$this->addColumn('role_id', array(
            'header'    =>__('Role ID'),
            'index'     =>'role_id',
            'align'     => 'right',
            'width'    => '50px'
        ));*/

        $this->addColumn('role_name', array(
            'header'    =>__('Role Name'),
            'index'     =>'role_name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/rolesGrid', array('user_id' => Mage::registry('permissions_user')->getUserId()));
    }

    protected function _getSelectedRoles()
    {
    	return Mage::registry('permissions_user')->getRoles();
	}

}
