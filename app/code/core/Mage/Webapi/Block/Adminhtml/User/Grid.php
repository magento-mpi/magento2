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
 * Web API permissions user grid
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Grid extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Initialize grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserGrid');
        $this->setDefaultSort('username');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Webapi_Model_Resource_Acl_User_Collection */
        $collection = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_User_Collection');
        $collection->joinRoles();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header' => Mage::helper('Mage_Webapi_Helper_Data')->__('ID'),
            'width' => 5,
            'align' => 'right',
            'sortable' => true,
            'index' => 'user_id'
        ));

        $this->addColumn('user_name', array(
            'header' => Mage::helper('Mage_Webapi_Helper_Data')->__('User Name'),
            'index' => 'user_name'
        ));

        $this->addColumn('role_name', array(
            'header' => Mage::helper('Mage_Webapi_Helper_Data')->__('Role'),
            'index' => 'role_name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get row action URL
     *
     * @param Varien_Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('user_id' => $row->getId()));
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/roles', array());
    }

}
