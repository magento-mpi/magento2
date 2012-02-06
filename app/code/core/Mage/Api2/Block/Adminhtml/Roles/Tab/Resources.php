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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for rendering resources list tab
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setSelectedResources(array $resources)
 * @method array getSelectedResources() getSelectedResources()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setExistsPrivileges(array $resources)
 * @method array getExistsPrivileges()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setConfigResources(Varien_Simplexml_Element $resources)
 * @method Varien_Simplexml_Element getConfigResources()
 * @method Mage_Api2_Model_Acl_Global_Role getRole()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setRole(Mage_Api2_Model_Acl_Global_Role $role)
 */
class Mage_Api2_Block_Adminhtml_Roles_Tab_Resources extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    const NAME_CHILDREN = 'children';
    /**
     * Role model
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected $_role;

    /**
     * Initialized
     *
     * @var bool
     */
    protected $_initialized = false;

    /**
     * Exist privileges
     *
     * @var array
     */
    protected $_privileges;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_role_section_resources')
                ->setDefaultDir(Varien_Db_Select::SQL_ASC)
                ->setDefaultSort('sort_order')
                ->setData('title', Mage::helper('api2')->__('Api Rules Information'))
                ->setData('use_ajax', true);
    }

    /**
     * Initialize block
     *
     * @return Mage_Api2_Block_Adminhtml_Roles_Tab_Resources
     * @throws Exception
     */
    protected function _init()
    {
        if ($this->_initialized) {
            return $this;
        }

        $role = $this->getRole();

        /** @var $config Mage_Api2_Model_Config */
        $config = Mage::getModel('api2/config');

        $resources = $config->getResourceGroups();
        $this->setConfigResources($resources);

        /** @var $privilegeSource Mage_Api2_Model_Acl_Global_Rule_Privilege */
        $privilegeSource = Mage::getModel('api2/acl_global_rule_privilege');
        $this->setExistsPrivileges($privilegeSource->toArray());

        $selectedIds = array();
        $permissions = $role->getResourcesPermissions();
        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        if (empty($permissions[$all])) {
            /** @var $status Mage_Api2_Model_Acl_Global_Rule */
            foreach ($permissions as $itemResourceId => $status) {
                if ($status == Mage_Api2_Model_Acl_Global_Rule_Permission::TYPE_ALLOW) {
                    $selectedIds[] = $itemResourceId;
                }
            }
        }
        $this->setSelectedResources($selectedIds);
        return $this;
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function getEverythingAllowed()
    {
        $this->_init();

        if (!$this->getRole()->getId()) {
            return true;
        }

        $resources = $this->getRole()->getResourcesPermissions();
        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        return !empty($resources[$all]);
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $this->_init();
        $resources = $this->getConfigResources();
        /** @var $helperRole Mage_Api2_Helper_Role */
        $helperRole = Mage::helper('api2/role');
        $data = $helperRole->getTreeResources(
            $resources,
            $this->getSelectedResources(),
            $this->getExistsPrivileges());

        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        $json = $helper->jsonEncode($data);

        return $json;
    }



    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('api2')->__('Role API Resources');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
