<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Api_Model_Resource_Roles _getResource()
 * @method Magento_Api_Model_Resource_Roles getResource()
 * @method int getParentId()
 * @method Magento_Api_Model_Roles setParentId(int $value)
 * @method int getTreeLevel()
 * @method Magento_Api_Model_Roles setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method Magento_Api_Model_Roles setSortOrder(int $value)
 * @method string getRoleType()
 * @method Magento_Api_Model_Roles setRoleType(string $value)
 * @method int getUserId()
 * @method Magento_Api_Model_Roles setUserId(int $value)
 * @method string getRoleName()
 * @method Magento_Api_Model_Roles setRoleName(string $value)
 * @method string getName()
 * @method Magento_Api_Model_Role setName() setName(string $name)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Roles extends Magento_Core_Model_Abstract
{
    /**
     * Filters
     *
     * @var array
     */
    protected $_filters;


    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Resource_Roles');
    }

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    public function getUsersCollection()
    {
        return Mage::getResourceModel('Magento_Api_Model_Resource_Roles_User_Collection');
    }

    public function getResourcesTree()
    {
        return $this->_buildResourcesArray(null, null, null, null, true);
    }

    public function getResourcesList()
    {
        return $this->_buildResourcesArray();
    }

    public function getResourcesList2D()
    {
        return $this->_buildResourcesArray(null, null, null, true);
    }

    public function getRoleUsers()
    {
        return $this->getResource()->getRoleUsers($this);
    }

    protected function _buildResourcesArray(
        Magento_Simplexml_Element $resource = null, $parentName = null, $level = 0, $represent2Darray = null,
        $rawNodes = false, $module = 'Magento_Adminhtml'
    ) {
        static $result;

        if (is_null($resource)) {
            $resource = Mage::getSingleton('Magento_Api_Model_Config')->getNode('acl/resources');
            $resourceName = null;
            $level = -1;
        } else {
            $resourceName = $parentName;
            if ($resource->getName()!='title' && $resource->getName()!='sort_order'
                && $resource->getName() != 'children'
            ) {
                $resourceName = (is_null($parentName) ? '' : $parentName.'/').$resource->getName();

                //assigning module for its' children nodes
                if ($resource->getAttribute('module')) {
                    $module = (string)$resource->getAttribute('module');
                }

                if ($rawNodes) {
                    $resource->addAttribute("aclpath", $resourceName);
                }

                $resource->title = (string)__((string)$resource->title);

                if ( is_null($represent2Darray) ) {
                    $result[$resourceName]['name']  = (string)$resource->title;
                    $result[$resourceName]['level'] = $level;
                } else {
                    $result[] = $resourceName;
                }
            }
        }

        $children = $resource->children();
        if (empty($children)) {
            if ($rawNodes) {
                return $resource;
            } else {
                return $result;
            }
        }
        foreach ($children as $child) {
            $this->_buildResourcesArray($child, $resourceName, $level+1, $represent2Darray, $rawNodes, $module);
        }
        if ($rawNodes) {
            return $resource;
        } else {
            return $result;
        }
    }

    /**
     * Filter data before save
     *
     * @return Magento_Api_Model_Roles
     */
    protected function _beforeSave()
    {
        $this->filter();
        parent::_beforeSave();
        return $this;
    }

    /**
     * Filter set data
     *
     * @return Magento_Api_Model_Roles
     */
    public function filter()
    {
        $data = $this->getData();
        if (!$this->_filters || !$data) {
            return $this;
        }
        /** @var $filter Magento_Core_Model_Input_Filter */
        $filter = Mage::getModel('Magento_Core_Model_Input_Filter');
        $filter->setFilters($this->_filters);
        $this->setData($filter->filter($data));
        return $this;
    }
}
