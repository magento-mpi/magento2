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
        $this->setUseAjax(true);
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
            'header'    =>__('ID'),
            'width'     =>5,
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'user_id'
        ));
        $this->addColumn('username', array(
            'header'    =>__('User Name'),
            'index'     =>'username'
        ));
        $this->addColumn('firstname', array(
            'header'    =>__('First Name'),
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>__('Last Name'),
            'index'     =>'lastname'
        ));
        $this->addColumn('email', array(
            'header'    =>__('Email'),
            'width'     =>40,
            'align'     =>'center',
            'index'     =>'email'
        ));
        $this->addColumn('action', array(
            'header'    =>__('Actions'),
            'filter'    =>false,
            'sortable'  =>false,
            'is_system' =>true,
            'type'      => 'action',
            'actions'   => array(
                array(
                    'caption' => __('Edit'),
                    'url' => Mage::getUrl('*/*/edituser/id/$user_id')
                ),
            )
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/userGrid', array('_current'=>true));
    }
}
