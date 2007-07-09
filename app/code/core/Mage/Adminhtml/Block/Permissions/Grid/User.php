<?php
/**
 * Users grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_Grid_User extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('customerGrid');
    }
    
    protected function _prepareCollection()
    {
        $collection =  Mage::getModel("permissions/users")->getCollection();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('id'), 
            'width'     =>5, 
            'align'     =>'center', 
            'sortable'  =>true, 
            'index'     =>'user_id'
        ));
        $this->addColumn('username', array(
            'header'    =>__('Username'), 
            'index'     =>'username'
        ));
        $this->addColumn('firstname', array(
            'header'    =>__('firstname'), 
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>__('lastname'), 
            'index'     =>'lastname'
        ));
        $this->addColumn('email', array(
            'header'    =>__('email'), 
            'width'     =>40, 
            'align'     =>'center', 
            'index'     =>'email'
        ));
        $this->addColumn('action', array(
            'header'    =>__('action'),
            'align'     =>'center',
            'format'    =>'<a href="'.Mage::getUrl('*/*/edituser/id/$user_id').'">'.__('edit').'</a>',
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true
        ));
        
        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));
        return parent::_prepareColumns();
    }
}
