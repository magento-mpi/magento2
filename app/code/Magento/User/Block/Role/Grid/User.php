<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Acl role user grid.
 *
 * @category   Magento
 * @package    Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Block_Role_Grid_User extends Magento_Backend_Block_Widget_Grid_Extended
{

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('role_user_id');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setDefaultFilter(array('in_role_users'=>1));
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
                $this->getCollection()->addFieldToFilter('user_id', array('in'=>$inRoleIds));
            } else {
                if ($inRoleIds) {
                    $this->getCollection()->addFieldToFilter('user_id', array('nin'=>$inRoleIds));
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
        $collection = Mage::getModel('Magento_User_Model_Role')->getUsersCollection();
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
            'options'   => array(
                '1' => __('Active'),
                '0' => __('Inactive')
            ),
        ));

        /*
        $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>5,
                'sortable'=>false,
                'filter'    =>false,
                'type' => 'action',
                'actions'   => array(
                                    array(
                                        'caption' => __('Remove'),
                                        'onClick' => 'role.deleteFromRole($role_id);'
                                    )
                                )
            )
        );
        */

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');
        return $this->getUrl('*/*/editrolegrid', array('rid' => $roleId));
    }

    public function getUsers($json=false)
    {
        if ( $this->getRequest()->getParam('in_role_user') != "" ) {
            return $this->getRequest()->getParam('in_role_user');
        }
        $roleId = ( $this->getRequest()->getParam('rid') > 0 ) ?
            $this->getRequest()->getParam('rid') :
            $this->_coreRegistry->registry('RID');
        $users  = Mage::getModel('Magento_User_Model_Role')->setId($roleId)->getRoleUsers();
        if (sizeof($users) > 0) {
            if ($json) {
                $jsonUsers = array();
                foreach ($users as $usrid) {
                    $jsonUsers[$usrid] = 0;
                }
                return Mage::helper('Magento_Core_Helper_Data')->jsonEncode((object)$jsonUsers);
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

