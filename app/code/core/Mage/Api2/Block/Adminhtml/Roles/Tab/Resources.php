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
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setSelectedResources() setSelectedResources()
 * @method array getSelectedResources() getSelectedResources()
 * @method Mage_Api2_Block_Adminhtml_Roles_Tab_Resources setConfigResources(Varien_Simplexml_Element $resources)
 * @method Varien_Simplexml_Element getConfigResources()
 */
class Mage_Api2_Block_Adminhtml_Roles_Tab_Resources extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('api2_rules_section')
                ->setDefaultDir(Varien_Db_Select::SQL_ASC)
                ->setDefaultSort('sort_order')
                ->setData('title', Mage::helper('api2')->__('Api Rules Information'))
                ->setData('use_ajax', true);

        $this->setTemplate('permissions/rolesedit.phtml');
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

        $selectedIds = array();
        $permissions = $role->getResourcesPermissionsPairs();
        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        if (!isset($permissions[$all])) {
            /** @var $status Mage_Api2_Model_Acl_Global_Rule */
            foreach ($permissions as $itemResourceId => $status) {
                if ($status == Mage_Api2_Model_Acl_Global_Rule_Permission::SOURCE_ALLOW) {
                    $selectedIds[] = $itemResourceId;
                }
            }
        }

        $this->setSelectedResources($selectedIds);

        return $this;
    }

    /**
     * Get role model
     *
     * @return Mage_Api2_Model_Acl_Global_Role
     */
    public function getRole()
    {
        if (null === $this->_role) {
            /** @var $tabs Mage_Api2_Block_Adminhtml_Roles_Tabs */
            $tabs = $this->getParentBlock();
            $role = $tabs->getRole();
            $this->_role = $role ? $role : Mage::getModel('api2/acl_global_role');
        }
        return $this->_role;
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

        $permissions = $this->getRole()->getResourcesPermissionsPairs();
        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        return isset($permissions[$all])
                && $permissions[$all] == Mage_Api2_Model_Acl_Global_Rule_Permission::SOURCE_ALLOW;
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
        $rootArray = $this->_getNodeJson($resources, 1);
        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('core');
        $json = $helper->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());

        return $json;
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Get Node Json
     *
     * @param Varien_Simplexml_Element|array $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = array();
        $selectedResources = $this->getSelectedResources();

        $group = false;
        if ($level != 0) {
            $type = (string) $node->type;
            if (!$type) {
                $group = true;
                $name = $node->getName();
                if ('resource_groups' != $name) {
                    $item['id'] = 'group-' . $name;
                }
                $item['text'] = (string) $node->title;
            } else {
                $item['id'] = 'resource-' . $type;
                $item['text'] = $this->__('%s (Resource)', (string) $node->title);
            }
            $item['sort_order'] = isset($node->sort_order) ? (string) $node->sort_order : 0;

            if (in_array($type, $selectedResources)) {
                $item['checked'] = true;
            }
        }
        if (isset($node->children)) {
            $children = $node->children->children();
        } else {
            $children = $node->children();
        }

        if (empty($children)) {
            return $item;
        }

        if ($children) {
            $item['children'] = array();
            /** @var $child Varien_Simplexml_Element */
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string) $child->title) {
                        continue;
                    }

                    if ($level != 0) {
                        $subNode = $this->_getNodeJson($child, $level + 1);
                        if (!$subNode) {
                            continue;
                        }
                        //if sub-node check then check current node
                        if (!empty($subNode['checked'])) {
                            $item['checked'] = true;
                        }
                        $item['children'][] = $subNode;
                    } else {
                        $item = $this->_getNodeJson($child, $level + 1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            } elseif ($group) {
                return null;
            }
        }
        return $item;
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
