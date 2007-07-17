<?php
/**
 * Acl role user grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Permissions_Role_Grid_User extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('role_name');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $roleId = $this->getRequest()->getParam('rid');
        $collection = Mage::getModel('permissions/roles')->getUsersCollection();
        $collection->setRoleFilter($roleId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('role_name',
            array(
                'header'=>__('Role user'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'role_name'
            )
        );

       $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>5,
                'sortable'=>false,
                'filter'    =>false,
                'type' => 'action',
                'actions'   => array(
                                    array(
                                        'caption' => __('Delete'),
                                        'onClick' => 'role.deleteFromRole($role_id);' . $this->getJsObjectName() . '.reload();'
                                    )
                                )
            )
        );
        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return Mage::getUrl('*/*/editrolegrid', array('rid' => $roleId));
    }
}