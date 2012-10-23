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
 * Web API User roles grid block
 *
 * @method Mage_Webapi_Block_Adminhtml_User_Edit setApiUser(Mage_Webapi_Model_Acl_User $user)
 * @method Mage_Webapi_Model_Acl_User getApiUser()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles extends Mage_Backend_Block_Widget_Grid_Extended
{
    /**
     * Initialize grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('permissionsUserRolesGrid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('asc');
        $this->setTitle(Mage::helper('Mage_Webapi_Helper_Data')->__('User Roles Information'));
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Webapi_Model_Resource_Acl_Role_Collection */
        $collection = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Role_Collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Webapi_Block_Adminhtml_User_Edit_Tab_Roles
     */
    protected function _prepareColumns()
    {
        $this->addColumn('role_id', array(
            'html_name' => 'role_id',
            'index' => 'role_id',
            'value' => $this->getApiUser()->getRoleId(),
            'header' => Mage::helper('Mage_Webapi_Helper_Data')->__('Assigned'),
            'type' => 'radio',
            'header_css_class' => 'a-center',
            'align' => 'center',
            'filter' => false,
            'sortable' => false,
            'required' => true
        ));

        $this->addColumn('role_name', array(
            'header' =>Mage::helper('Mage_Webapi_Helper_Data')->__('Role Name'),
            'index' =>'role_name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Get Web API User roles grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/rolesgrid', array('user_id' => $this->getApiUser()->getId()));
    }
}
