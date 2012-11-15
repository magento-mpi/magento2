<?php
/**
 * Acl role user grid
 *
 * @copyright {}
 */
class Mage_Webapi_Block_Adminhtml_Role_Grid_User extends Mage_Backend_Block_Widget_Grid_Extended
{
    /**
     * Internal Constructor
     */
    protected function _construct()
    {
        $this->setDefaultSort('role_user_id');
        $this->setDefaultDir('asc');
        $this->setId('roleUserGrid');
        $this->setDefaultFilter(array('in_role_users' => 1));
        $this->setUseAjax(true);
        parent::_construct();
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
        $collection = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')->getCollection();
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
        $users = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')->getRoleUsers($roleId);
        if (count($users) > 0) {
            if ($json) {
                $jsonUsers = array();
                foreach ($users as $userId) {
                    $jsonUsers[$userId] = 0;
                }
                return Mage::getObjectManager()->get('Mage_Core_Helper_Data')->jsonEncode((object)$jsonUsers);
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
