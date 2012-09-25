<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API ACL roles grid
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Grid extends Mage_Backend_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('webapiRoleGrid');
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('role_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('role_id', array(
            'header'    => Mage::helper('Mage_Webapi_Helper_Data')->__('ID'),
            'index'     => 'role_id',
            'align'     => 'right',
            'width'     => '50px'
        ));

        $this->addColumn('role_name', array(
            'header'    => Mage::helper('Mage_Webapi_Helper_Data')->__('Role Name'),
            'index'     => 'role_name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/roleGrid', array('_current' => true));
    }

    /**
     * Get grid row URL
     * @param Mage_Webapi_Model_Acl_Role $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('role_id' => $row->getRoleId()));
    }
}
