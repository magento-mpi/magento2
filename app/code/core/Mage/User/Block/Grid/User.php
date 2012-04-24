<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Users grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Permissions_Grid_User extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('username');
        $this->setDefaultDir('asc');
    }

    protected function _prepareCollection()
    {
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    =>Mage::helper('Mage_Adminhtml_Helper_Data')->__('ID'),
            'width'     =>5,
            'align'     =>'right',
            'sortable'  =>true,
            'index'     =>'user_id'
        ));
        $this->addColumn('username', array(
            'header'    =>Mage::helper('Mage_Adminhtml_Helper_Data')->__('User Name'),
            'index'     =>'username'
        ));
        $this->addColumn('firstname', array(
            'header'    =>Mage::helper('Mage_Adminhtml_Helper_Data')->__('First Name'),
            'index'     =>'firstname'
        ));
        $this->addColumn('lastname', array(
            'header'    =>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Last Name'),
            'index'     =>'lastname'
        ));
        $this->addColumn('email', array(
            'header'    =>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Email'),
            'width'     =>40,
            'align'     =>'left',
            'index'     =>'email'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edituser', array('id' => $row->getUserId()));
    }

}

