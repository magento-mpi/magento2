<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API role resource list
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
    public function __construct()
    {
        parent::__construct();

        $role = $this->getApiRole();
        $role_id = $role ? $role->getRoleId() : null;

        $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesArray();

        $rules_set = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
            ->getByRoles($role_id)->load();

        $selrids = array();
        foreach ($rules_set->getItems() as $item) {
            if (array_key_exists(strtolower($item->getResource_id()), $resources)
                && $item->getApiPermission() == 'allow')
            {
                $resources[$item->getResource_id()]['checked'] = true;
                array_push($selrids, $item->getResource_id());
            }
        }

        $this->setSelectedResources($selrids);
    }

    /**
     *
     * @return bool
     */
    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    /**
     *
     * @return string
     */
    public function getResTreeJson()
    {
        /** @var $resources DOMNodeList */
        $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesList();

        if ($resources) {
            $resourceArray = array();
            /** @var $res DOMElement */
            foreach ($resources as $res) {
                $resourceArray[] = $this->_getNodeJson($res);
            }
            return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($resourceArray);
        }

        return '';
    }

    /**
     *
     * @param DOMElement $node
     * @param int $level
     * @return mixed
     */
    protected function _getNodeJson($node)
    {
        $item = array();
        $selres = $this->getSelectedResources();

        $item['text'] = (string) $node->getAttribute('title');
        $item['id'] = (string) $node->getAttribute('id');

        if (in_array($item['id'], $selres)) {
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

        return $item;
    }
}
