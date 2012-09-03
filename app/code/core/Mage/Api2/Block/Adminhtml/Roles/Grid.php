<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Roles grid block
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Roles_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Construct grid block
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('rolesGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('entity_id')
            ->setDefaultDir(Varien_Db_Select::SQL_DESC);
    }

    /**
     * Prepare collection
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
        $collection = Mage::getModel('Mage_Api2_Model_Acl_Global_Role')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare columns
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('ID'),
            'index'  => 'entity_id',
            'align'  => 'right',
            'width'  => '50px',
        ));

        $this->addColumn('role_name', array(
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('Role Name'),
            'index'  => 'role_name',
            'escape' => true,
        ));

        $this->addColumn('tole_user_type', array(
            'header'         => Mage::helper('Mage_Oauth_Helper_Data')->__('User Type'),
            'sortable'       => false,
            'frame_callback' => array($this, 'decorateUserType')
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('Mage_Oauth_Helper_Data')->__('Created At'),
            'index'  => 'created_at'
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Get grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Get row URL
     *
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @return string|null
     */
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Api2::rest_roles_edit')) {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
        return null;
    }

    /**
     * Decorate 'User Type' column
     *
     * @param string $renderedValue Rendered value
     * @param Mage_Api2_Model_Acl_Global_Role $row
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateUserType($renderedValue, $row, $column, $isExport)
    {
        switch ($row->getEntityId()) {
            case Mage_Api2_Model_Acl_Global_Role::ROLE_GUEST_ID:
                $userType = Mage::helper('Mage_Api2_Helper_Data')->__('Guest');
                break;
            case Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID:
                $userType = Mage::helper('Mage_Api2_Helper_Data')->__('Customer');
                break;
            default:
                $userType = Mage::helper('Mage_Api2_Helper_Data')->__('Admin');
                break;
        }
        return $userType;
    }
}
