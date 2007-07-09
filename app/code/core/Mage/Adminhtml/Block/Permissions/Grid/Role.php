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
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        //$this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("permissions/roles")->getCollection();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        /*$this->addColumn('id', array(
            'header'    =>__('id'), 
            'width'     =>5, 
            'align'     =>'center', 
            'sortable'  =>true, 
            'index'     =>'role_id'
        ));*/
        $this->addColumn('rolename', array(
            'header'    =>__('Role name'), 
            'index'     =>'role_name'
        ));
        $this->addColumn('action', array(
            'header'    =>__('action'),
            'align'     =>'center',
            'format'    =>'<a href="'.Mage::getUrl('*/*/editrole/id/$role_id').'">'.__('edit').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));
        
        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));
        return parent::_prepareColumns();
    }
}
