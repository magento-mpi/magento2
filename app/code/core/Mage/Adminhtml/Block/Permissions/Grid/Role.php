<?php
/**
 * roles grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_Grid_Role extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('roleGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("permissions/roles")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rolename', array(
            'header'    =>__('Role Name'),
            'index'     =>'role_name'
        ));
        $this->addColumn('action', array(
            'header'    =>__('Action'),
            'align'     =>'center',
            'format'    =>'<a href="'.Mage::getUrl('*/*/editrole/rid/$role_id').'">'.__('Edit').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'width'     =>'50px',
            'is_system' =>true
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', array('_current'=>true));
    }
}
