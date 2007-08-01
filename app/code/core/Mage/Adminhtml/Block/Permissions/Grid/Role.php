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
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('role_name');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("permissions/roles")->getCollection();
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

    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/editrole', array('rid' => $row->getRoleId()));
    }
}
