<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rolesedit Tab Display Block
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Block_Role_Tab_Edit extends Mage_Backend_Block_Widget_Form
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_User_Helper_Data')->__('Role Resources');
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

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        /** @var $acl Magento_Acl */
        $acl = Mage::getModel('Mage_Backend_Model_Auth_Session')->getAcl();
        $rulesSet = Mage::getResourceModel('Mage_User_Model_Resource_Rules_Collection')->getByRoles($rid)->load();

        $selectedResourceIds = array();

        foreach ($rulesSet->getItems() as $item) {
            $itemResourceId = $item->getResource_id();
            if ($acl->has($itemResourceId) && $item->getPermission() == 'allow') {
                array_push($selectedResourceIds, $itemResourceId);
            }
        }

        $this->setSelectedResources($selectedResourceIds);

        $this->setTemplate('role/edit.phtml');
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function isEverythingAllowed()
    {
        return in_array(Mage_Backend_Model_Acl_Config::ACL_RESOURCE_ALL, $this->getSelectedResources());
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        /** @var $resources DOMNodeList */
        $resources = Mage::getSingleton('Mage_Backend_Model_Acl_Config')->getAclResources();

        $rootArray = $this->_getNodeJson($resources->item(1), 1);

        $json = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
            isset($rootArray['children']) ? $rootArray['children'] : array()
        );

        return $json;
    }

    /**
     * Compare two nodes of the Resource Tree
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortTree($nodeA, $nodeB)
    {
        return $nodeA['sortOrder']<$nodeB['sortOrder'] ? -1 : ($nodeA['sortOrder']>$nodeB['sortOrder'] ? 1 : 0);
    }

    /**
     * Get Node Json
     *
     * @param mixed $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson(DomElement $node, $level = 0)
    {
        $item = array();
        $selres = $this->getSelectedResources();
        if ($level != 0) {
            $item['text'] = Mage::helper('Mage_User_Helper_Data')->__((string)$node->getAttribute('title'));
            // @codingStandardsIgnoreStart
            $item['sortOrder'] = $node->hasAttribute('sortOrder') ? (string)$node->getAttribute('sortOrder') : 0;
            // @codingStandardsIgnoreEnd
            $item['id'] = (string)$node->getAttribute('id');

            if (in_array($item['id'], $selres)) {
                $item['checked'] = true;
            }
        }
        $children = $node->childNodes;
        if (!empty($children)) {
            $item['children'] = array();
            //$item['cls'] = 'fiche-node';
            foreach ($children as $child) {
                if ($child instanceof DOMElement) {
                    if (!(string)$child->getAttribute('title')) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level+1);
                    } else {
                        $item = $this->_getNodeJson($child, $level+1);
                    }
                }
            }
            usort($item['children'], array($this, '_sortTree'));
        }
        return $item;
    }
}
