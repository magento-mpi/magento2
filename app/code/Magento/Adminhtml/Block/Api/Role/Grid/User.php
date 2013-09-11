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
 * Acl role user grid
 */
class Magento_Adminhtml_Block_Api_Role_Grid_User extends Magento_Adminhtml_Block_Widget_Grid
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $helper
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $helper,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($helper, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('role_user_id');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setDefaultFilter(array('in_role_users' => 1));
        $this->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_role_users') {
            $inRoleIds = $this->getUsers();
            if (empty($inRoleIds)) {
                $inRoleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', array('in' => $inRoleIds));
            } else {
                if ($inRoleIds) {
                    $this->getCollection()->addFieldToFilter('user_id', array('nin' => $inRoleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $roleId = $this->getRequest()->getParam('rid');
        $this->_coreRegistry->register('RID', $roleId);
        $collection = Mage::getModel('Magento_Api_Model_Roles')->getUsersCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_role_users', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_role_users',
            'values'    => $this->getUsers(),
            'align'     => 'center',
            'index'     => 'user_id'
        ));

        $this->addColumn('role_user_id', array(
            'header'    =>__('User ID'),
            'width'     =>5,
            'align'     =>'left',
            'sortable'  =>true,
            'index'     =>'user_id'
        ));

        $this->addColumn('role_user_username', array(
            'header'    =>__('User Name'),
            'align'     =>'left',
            'index'     =>'username'
        ));

        $this->addColumn('role_user_firstname', array(
            'header'    =>__('First Name'),
            'align'     =>'left',
            'index'     =>'firstname'
        ));

        $this->addColumn('role_user_lastname', array(
            'header'    =>__('Last Name'),
            'align'     =>'left',
            'index'     =>'lastname'
        ));

        $this->addColumn('role_user_email', array(
            'header'    =>__('Email'),
            'width'     =>40,
            'align'     =>'left',
            'index'     =>'email'
        ));

        $this->addColumn('role_user_is_active', array(
            'header'    => __('Status'),
            'index'     => 'is_active',
            'align'     =>'left',
            'type'      => 'options',
            'options'   => array('1' => __('Active'), '0' => __('Inactive')),
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return $this->getUrl('*/*/editrolegrid', array('rid' => $roleId));
    }

    public function getUsers($json = false)
    {
        if ( $this->getRequest()->getParam('in_role_user') != "" ) {
            return $this->getRequest()->getParam('in_role_user');
        }
        if ($this->getRequest()->getParam('rid') > 0) {
            $roleId = $this->getRequest()->getParam('rid');
        } else {
            $roleId = $this->_coreRegistry->registry('RID');
        }

        $users  = Mage::getModel('Magento_Api_Model_Roles')->setId($roleId)->getRoleUsers();
        if (sizeof($users) > 0) {
            if ($json) {
                $jsonUsers = Array();
                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }
                return $this->_coreData->jsonEncode((object)$jsonUsers);
            } else {
                return array_values($users);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return array();
            }
        }
    }

}

