<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * roles grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Api_Grid_Role extends Magento_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('roleGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('role_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection =  Mage::getModel('Magento_Api_Model_Roles')->getCollection();
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
            'header'    =>__('Role'),
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
        return $this->getUrl('*/*/editrole', array('rid' => $row->getRoleId()));
    }
}
