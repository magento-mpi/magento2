<?php
class Mage_Adminhtml_Block_Permissions_User_Edit_Tab_Roles extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('role_name');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('permissions/role_collection');
        $collection->setUserFilter(Mage::registry('permissions_user')->getUserId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('role_id', array(
            'header'    =>__('ID'),
            'index'     =>'role_id',
            'align'     => 'right',
            'width'    => '50px'
        ));

        $this->addColumn('role_name', array(
            'header'    =>__('Role Name'),
            'index'     =>'role_name'
        ));

        return parent::_prepareColumns();
    }

}
