<?php
/**
 * Acl role user grid
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role_Grid_User extends Mage_Backend_Block_Widget_Grid_Extended
{
    /**
     * @var Mage_Webapi_Model_Acl_User
     */
    protected $_userModel;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Webapi_Model_Acl_User $userModel
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Webapi_Model_Acl_User $userModel,
        array $data = array()
    ) {
        $this->_userModel = $userModel;

        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $data
        );

        $this->setDefaultSort('role_user_id');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setDefaultFilter(array('in_role_users' => 1));
        $this->setUseAjax(true);
    }

    /**
     * Add column to filter
     *
     * @param Mage_Backend_Block_Widget_Grid_Column $column
     * @return Mage_Webapi_Block_Adminhtml_Role_Grid_User
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_role_users') {
            $inRoleIds = $this->_getUsers();
            if (empty($inRoleIds)) {
                $inRoleIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('user_id', array('in' => $inRoleIds));
            } elseif ($inRoleIds) {
                $this->getCollection()->addFieldToFilter('user_id', array('nin' => $inRoleIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare grid collection
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_Webapi_Model_Resource_Acl_User_Collection */
        $collection = $this->_userModel->getCollection();
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
        $this->addColumn('in_role_users', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_role_users',
            'values'    => $this->_getUsers(),
            'align'     => 'center',
            'index'     => 'user_id'
        ));

        $this->addColumn('role_user_id', array(
            'header'    => $this->__('User ID'),
            'width'     => 20,
            'align'     => 'left',
            'sortable'  => true,
            'index'     => 'user_id'
        ));

        $this->addColumn('role_user_contactemail', array(
            'header'    => $this->__('Contact Email'),
            'align'     => 'left',
            'index'     => 'contact_email'
        ));

        $this->addColumn('role_user_apikey', array(
            'header'    => $this->__('API Key'),
            'align'     => 'left',
            'index'     => 'api_key'
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
        $roleId = $this->getRequest()->getParam('role_id');
        return $this->getUrl('*/*/editrolegrid', array('role_id' => $roleId));
    }

    /**
     * Users list accessor
     *
     * @param bool $json
     * @return array|mixed|string
     */
    protected function _getUsers($json = false)
    {
        if ($this->getRequest()->getParam('in_role_user') != "") {
            return $this->getRequest()->getParam('in_role_user');
        }

        $roleId = (int)$this->getRequest()->getParam('role_id');
        $users = $this->_userModel->getRoleUsers($roleId);
        if (count($users) > 0) {
            if ($json) {
                $jsonUsers = array();
                foreach ($users as $userId) {
                    $jsonUsers[$userId] = 0;
                }
                return Mage::helper('Mage_Core_Helper_Data')->jsonEncode((object)$jsonUsers);
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
