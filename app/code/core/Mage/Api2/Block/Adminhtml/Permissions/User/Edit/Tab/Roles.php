<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 role list for user permissions
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Block_Adminhtml_Permissions_User_Edit_Tab_Roles
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('REST Role');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('api2')->__('REST Role');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    public function __construct()
    {
        parent::__construct();
        $this->setId('api2_roles_section')
            ->setDefaultSort('sort_order')
            ->setDefaultDir(Varien_Db_Select::SQL_ASC)
            ->setTitle(Mage::helper('api2')->__('REST Roles Information'))
            ->setUseAjax(true);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'assigned_user_role') {
            $userRoles = $this->_getSelectedRoles();
            if (empty($userRoles)) {
                $userRoles = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $userRoles));
            } else {
                if($userRoles) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $userRoles));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        /** @var $collection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
        $collection = Mage::getResourceModel('api2/acl_global_role_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('assigned_user_role', array(
            'header_css_class' => 'a-center',
            'header'    => Mage::helper('api2')->__('Assigned'),
            'type'      => 'radio',
            'html_name' => 'roles[]',
            'values'    => $this->_getSelectedRoles(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));

        $this->addColumn('role_name', array(
            'header'    => Mage::helper('api2')->__('Role Name'),
            'index'     => 'role_name'
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/api2_roles/rolesGrid', array('user_id' => Mage::registry('permissions_user')
            ->getUserId()));
    }

    protected function _getSelectedRoles($json = false)
    {
        if ($this->getRequest()->getParam('user_roles') != '') {
            return $this->getRequest()->getParam('user_roles');
        }
        /* @var $user Mage_Admin_Model_User */
        $user = Mage::registry('permissions_user');

        /** @var $collection Mage_Api2_Model_Resource_Acl_Global_Role_Collection */
        $collection = Mage::getResourceModel('api2/acl_global_role_collection');
        $collection->addFilterByAdminId($user->getId());

        $userRoles = $collection->getAllIds();

        if ($json) {
            $jsonRoles = array();
            foreach($userRoles as $roleId) {
                $jsonRoles[$roleId] = 0;
            }

            /** @var $coreHelper Mage_Core_Helper_Data */
            $coreHelper = Mage::helper('core');

            return $coreHelper->jsonEncode((object) $jsonRoles);
        } else {
            return $userRoles;
        }
    }
}
