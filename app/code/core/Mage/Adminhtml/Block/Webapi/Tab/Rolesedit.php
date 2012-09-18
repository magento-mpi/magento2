<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Webapi_Tab_Rolesedit extends Mage_Adminhtml_Block_Widget_Form {

    public function __construct() {
        parent::__construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesList();

        $rules_set = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
            ->getByRoles($rid)->load();

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

        $this->setTemplate('api/rolesedit.phtml');
    }

    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    public function getResTreeJson()
    {
        $rid = Mage::app()->getRequest()->getParam('rid', false);
        $resources = Mage::getModel('Mage_Webapi_Model_Acl_Role')->getResourcesTree();

        if ($resources) {
            $rootArray = $this->_getNodeJson($resources,1);
            $json = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(isset($rootArray['children']) ? $rootArray['children'] : array());
            return $json;
        }

        return '';
    }

    protected function _sortTree($a, $b)
    {
        return $a['sort_order']<$b['sort_order'] ? -1 : ($a['sort_order']>$b['sort_order'] ? 1 : 0);
    }


    protected function _getNodeJson($node, $level=0)
    {
        $item = array();
        $selres = $this->getSelectedResources();

        if ($level != 0) {
            $item['text']= (string)$node->title;
            $item['sort_order']= isset($node->sort_order) ? (string)$node->sort_order : 0;
            $item['id']  = (string)$node->attributes()->aclpath;

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
                if ($child->getName()!='title' && $child->getName()!='sort_order' && $child->attributes()->module) {
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
