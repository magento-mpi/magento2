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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice API2 role helper
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_Tree extends Mage_Core_Helper_Abstract
{
    /**#@+
     * Names
     */
    const NAME_CHILDREN         = 'children';
    const NAME_PRIVILEGE        = 'privilege';
    const NAME_RESOURCE         = 'resource';
    const NAME_RESOURCE_GROUPS  = 'resource_groups';
    const NAME_GROUP            = 'group';
    /**#@-*/

    /**
     * Separator for tree ID
     */
    const ID_SEPARATOR = '-';

    /**
     * Resources permissions
     *
     * @var array
     */
    protected $_resourcesPermissions;

    /**
     * Resources from config model
     *
     * @var array
     */
    protected $_resourcesConfig;

    /**
     * Exist privileges
     *
     * @var array
     */
    protected $_existPrivileges;

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
     * Initialize block
     *
     * @return Mage_Api2_Model_Acl_Global_Rule_Tree
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

        $this->_resourcesConfig = $config->getResourceGroups();

        /** @var $privilegeSource Mage_Api2_Model_Acl_Global_Rule_Privilege */
        $privilegeSource = Mage::getModel('api2/acl_global_rule_privilege');
        $this->_existPrivileges = $privilegeSource->toArray();

        if ($role) {
            $this->_resourcesPermissions = $role->getAclResourcesPermissions();
        }
        return $this;
    }

    /**
     * Convert to array serialized post data from tree grid
     *
     * @return array
     */
    public function getPostResourcesPrivleges()
    {
        $isAll = Mage::app()->getRequest()->getParam(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL);
        if ($isAll) {
            $resources = array(Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL);
        } else {
            $resources = array();
            $checkedResources = explode(',', Mage::app()->getRequest()->getParam('resource'));
            $prefixResource  = self::NAME_RESOURCE  . self::ID_SEPARATOR;
            $prefixPrivilege = self::NAME_PRIVILEGE . self::ID_SEPARATOR;
            $nameResource = null;
            foreach ($checkedResources as $i => $item) {
                if (0 === strpos($item, $prefixResource)) {
                    $nameResource = substr($item, mb_strlen($prefixResource, 'UTF-8'));
                    $resources[$nameResource] = array();
                } elseif (0 === strpos($item, $prefixPrivilege)) {
                    $name = substr($item, mb_strlen($prefixPrivilege, 'UTF-8'));
                    $namePrivilege = str_replace($nameResource . self::ID_SEPARATOR, '', $name);
                    $resources[$nameResource][$namePrivilege] = 1;
                } else {
                    unset($checkedResources[$i]);
                }
            }
        }
        return $resources;
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function getEverythingAllowed()
    {
        $this->_init();

        if (!$this->getRole() || !$this->getRole()->getId()) {
            return true;
        }

        $resources = $this->getRole()->getAclResourcesPermissions();
        $all = Mage_Api2_Model_Acl_Global_Rule::RESOURCE_ALL;
        return !empty($resources[$all]);
    }

    /**
     * Get tree resources
     *
     * @return array
     */
    public function getTreeResources()
    {
        $this->_init();
        $root = $this->_getTreeNode($this->_resourcesConfig, 1);
        return isset($root[self::NAME_CHILDREN]) ? $root[self::NAME_CHILDREN] : array();
    }

    /**
     * Get tree node
     *
     * @param Varien_Simplexml_Element|array $node
     * @param int $level
     * @return array
     */
    protected function _getTreeNode($node, $level = 0)
    {
        $item = array();

        $isResource = false;
        $isGroup    = false;
        $type       = null;

        if ($level != 0) {
            $type = (string) $node->type;
            if (!$type) {
                $name = $node->getName();
                if (self::NAME_RESOURCE_GROUPS != $name) {
                    $isGroup = true;
                    $item['id'] = self::NAME_GROUP . self::ID_SEPARATOR . $name;
                }
                $item['text'] = (string) $node->title;
            } else {
                $isResource = true;
                $item['id'] = self::NAME_RESOURCE . self::ID_SEPARATOR . $type;
                $item['text'] = $this->__('%s (Resource)', (string) $node->title);
            }
            $item['checked'] = false;
            $item['sort_order'] = isset($node->sort_order) ? (string) $node->sort_order : 0;
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
            $item[self::NAME_CHILDREN] = array();

            if ($isResource) {
                //add privileges
                if ($node->privileges) {
                    $allowed = $node->privileges->asArray();
                } else {
                    $allowed = array();
                }

                if (!$allowed) {
                    return null;
                }

                $cnt = 0;
                foreach ($this->_existPrivileges as $key => $title) {
                    if (empty($allowed[$key])) {
                        continue;
                    }
                    $checked = !empty($this->_resourcesPermissions[$type]['privileges'][$key]);
                    $item['checked'] = $checked ? $checked : $item['checked'];
                    $item[self::NAME_CHILDREN][] = array(
                        'id'   => self::NAME_PRIVILEGE . self::ID_SEPARATOR . $type . self::ID_SEPARATOR . $key,
                        'text' => $title,
                        'checked' => $checked,
                        'sort_order' => ++$cnt,
                    );
                }
            }

            /** @var $child Varien_Simplexml_Element */
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string) $child->title) {
                        continue;
                    }

                    if ($level != 0) {
                        $subNode = $this->_getTreeNode($child, $level + 1);
                        if (!$subNode) {
                            continue;
                        }
                        //if sub-node check then check current node
                        if (!empty($subNode['checked'])) {
                            $item['checked'] = true;
                        }
                        $item[self::NAME_CHILDREN][] = $subNode;
                    } else {
                        $item = $this->_getTreeNode($child, $level + 1);
                    }
                }
            }
            if (!empty($item[self::NAME_CHILDREN])) {
                usort($item[self::NAME_CHILDREN], array($this, '_sortTree'));
            } elseif ($isGroup) {
                //skip empty group
                return null;
            }
        }
        return $item;
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
     * Set role
     *
     * @param Mage_Api2_Model_Acl_Global_Role $role
     * @return Mage_Api2_Model_Acl_Global_Rule_Tree
     */
    public function setRole($role)
    {
        $this->_role = $role;
        return $this;
    }

    /**
     * Get role
     *
     * @return Mage_Api2_Model_Acl_Global_Role
     */
    public function getRole()
    {
        return $this->_role;
    }
}
