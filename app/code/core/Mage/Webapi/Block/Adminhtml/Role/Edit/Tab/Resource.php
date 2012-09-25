<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API role resource tab
 *
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit setApiRole(Mage_Webapi_Model_Acl_Role $role)
 * @method Mage_Webapi_Model_Acl_Role getApiRole()
 * @method Mage_Webapi_Block_Adminhtml_Role_Edit setSelectedResources(array $selrids)
 * @method array getSelectedResources()
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource extends Mage_Backend_Block_Widget_Form
{
    /**
     * Prepare Form
     *
     * @return Mage_Webapi_Block_Adminhtml_Role_Edit_Tab_Resource
     */
    protected function _prepareForm()
    {
        /** @var $role Mage_Webapi_Model_Acl_Role */
        $role = $this->getApiRole();

        if ($role->getRoleId()) {
            $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesArray();
            $rulesSet = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
                ->getByRoles($role->getRoleId())->load();

            $selectedRoleIds = array();
            foreach ($rulesSet->getItems() as $item) {
                $resourceId = $item->getResourceId();
                if (in_array($resourceId, $resources)
                    || $resourceId == Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID) {
                    array_push($selectedRoleIds, $resourceId);
                }
            }

            $this->setSelectedResources($selectedRoleIds);
        } else {
            $this->setSelectedResources(array());
        }

        return parent::_prepareForm();
    }

    /**
     * Check resource access is set to "All"
     * @return bool
     */
    public function getEverythingAllowed()
    {
        return in_array(Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID, $this->getSelectedResources());
    }

    /**
     * Get resource tree as JSON
     *
     * @return string
     */
    public function getResourceTreeJson()
    {
        /** @var $resources DOMNodeList */
        $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesList();

        if ($resources && $resources->length == 1
            && (string) $resources->item(0)->getAttribute('id')
                == Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID
            && $resources->item(0)->childNodes) {

            $resourceArray = $this->_getNodeJson($resources->item(0));
            if (!empty($resourceArray['children'])) {
                return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($resourceArray['children']);
            }
        }

        return '';
    }

    /**
     * Sorting function for array sorting
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }

    /**
     * Recursive creation of resource tree
     *
     * @param DOMElement $node
     * @return array
     */
    protected function _getNodeJson($node)
    {
        $item = array();
        $selRes = $this->getSelectedResources();

        $item['id'] = (string) $node->getAttribute('id');
        $item['text'] = (string) $node->getAttribute('title');
        $sortOrder = (string) $node->getAttribute('sort_order');
        $item['sort_order']= !empty($sortOrder) ? (int) $sortOrder : 0;

        if (in_array($item['id'], $selRes)) {
            $item['checked'] = true;
        }

        if (empty($node->childNodes)) {
            return $item;
        }

        $item['children'] = array();
        foreach ($node->childNodes as $child) {
            if ($child instanceof DOMElement) {
                $item['children'][] = $this->_getNodeJson($child);
            }
        }

        if (!empty($item['children'])) {
            usort($item['children'], array($this, '_sortTree'));
        }

        return $item;
    }
}
