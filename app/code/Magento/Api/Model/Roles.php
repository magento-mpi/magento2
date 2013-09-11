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
 * @method \Magento\Api\Model\Resource\Roles _getResource()
 * @method \Magento\Api\Model\Resource\Roles getResource()
 * @method int getParentId()
 * @method \Magento\Api\Model\Roles setParentId(int $value)
 * @method int getTreeLevel()
 * @method \Magento\Api\Model\Roles setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\Api\Model\Roles setSortOrder(int $value)
 * @method string getRoleType()
 * @method \Magento\Api\Model\Roles setRoleType(string $value)
 * @method int getUserId()
 * @method \Magento\Api\Model\Roles setUserId(int $value)
 * @method string getRoleName()
 * @method \Magento\Api\Model\Roles setRoleName(string $value)
 * @method string getName()
 * @method \Magento\Api\Model\Role setName() setName(string $name)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model;

class Roles extends \Magento\Core\Model\AbstractModel
{
    /**
     * Filters
     *
     * @var array
     */
    protected $_filters;


    protected function _construct()
    {
        $this->_init('Magento\Api\Model\Resource\Roles');
    }

    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    public function getUsersCollection()
    {
        return \Mage::getResourceModel('Magento\Api\Model\Resource\Roles\User\Collection');
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
        \Magento\Simplexml\Element $resource = null, $parentName = null, $level = 0, $represent2Darray = null,
        $rawNodes = false, $module = 'Magento_Adminhtml'
    ) {
        static $result;

        if (is_null($resource)) {
            $resource = \Mage::getSingleton('Magento\Api\Model\Config')->getNode('acl/resources');
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
     * @return \Magento\Api\Model\Roles
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
     * @return \Magento\Api\Model\Roles
     */
    public function filter()
    {
        $data = $this->getData();
        if (!$this->_filters || !$data) {
            return $this;
        }
        /** @var $filter \Magento\Core\Model\Input\Filter */
        $filter = \Mage::getModel('Magento\Core\Model\Input\Filter');
        $filter->setFilters($this->_filters);
        $this->setData($filter->filter($data));
        return $this;
    }
}
