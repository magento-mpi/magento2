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
class Mage_User_Block_Tab_Rolesedit extends Mage_Backend_Block_Widget_Form
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

        $resources = Mage::getModel('Mage_Admin_Model_Roles')->getResourcesList();

        $rules_set = Mage::getResourceModel('Mage_Admin_Model_Resource_Rules_Collection')->getByRoles($rid)->load();

        $selrids = array();

        foreach ($rules_set->getItems() as $item) {
            $itemResourceId = $item->getResource_id();
            if (array_key_exists(strtolower($itemResourceId), $resources) && $item->getPermission() == 'allow') {
                $resources[$itemResourceId]['checked'] = true;
                array_push($selrids, $itemResourceId);
            }
        }

        $this->setSelectedResources($selrids);

        $this->setTemplate('rolesedit.phtml');
    }

    /**
     * Check if everything is allowed
     *
     * @return boolean
     */
    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    /**
     * Get Json Representation of Resource Tree
     *
     * @return string
     */
    public function getResTreeJson()
    {
        $rid = Mage::app()->getRequest()->getParam('rid', false);
        $resources = Mage::getModel('Mage_Admin_Model_Roles')->getResourcesTree();

        $rootArray = $this->_getNodeJson($resources->admin, 1);

        $json = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());

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
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }

    /**
     * Get Node Json
     *
     * @param mixed $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 0)
    {
        $item = array();
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text'] = Mage::helper('Mage_User_Helper_Data')->__((string)$node->title);
            $item['sort_order'] = isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id'] = (string)$node->attributes()->aclpath;

            if (in_array($item['id'], $selres))
                $item['checked'] = true;
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
            //$item['cls'] = 'fiche-node';
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order') {
                    if (!(string)$child->title) {
                        continue;
                    }
                    if ($level != 0) {
                        $item['children'][] = $this->_getNodeJson($child, $level+1);
                    } else {
                        $item = $this->_getNodeJson($child, $level+1);
                    }
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }
        return $item;
    }
}
