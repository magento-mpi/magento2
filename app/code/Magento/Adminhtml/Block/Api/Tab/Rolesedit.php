<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Api_Tab_Rolesedit extends Magento_Adminhtml_Block_Widget_Form {

    protected $_template = 'api/rolesedit.phtml';


    protected function _construct() {
        parent::_construct();

        $rid = Mage::app()->getRequest()->getParam('rid', false);

        $resources = Mage::getModel('Magento_Api_Model_Roles')->getResourcesList();

        $rules_set = Mage::getResourceModel('Magento_Api_Model_Resource_Rules_Collection')->getByRoles($rid)->load();

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


        //->assign('resources', $resources);
        //->assign('checkedResources', join(',', $selrids));
    }

    /**
     * Get is everything allowed
     *
     * @return bool
     */
    public function getEverythingAllowed()
    {
        return in_array('all', $this->getSelectedResources());
    }

    /**
     * Get Resource Tree
     *
     * @return array
     */
    public function getTree()
    {
        $resource = Mage::getModel('Magento_Api_Model_Roles')->getResourcesTree();
        $rootArray = $this->_mapResources($resource);
        return $rootArray['children'];
    }

    /**
     * Map resources
     *
     * @param $resources
     * @return array
     */
    protected function _mapResources($resources)
    {
        $item = array();

        $item['data'] = (string)$resources->title;
        $item['sort_order']= isset($resources->sort_order) ? (string)$resources->sort_order : 0;
        $item['attr']['data-id'] = (string)$resources->attributes()->aclpath;

        if (isset($resources->children)) {
            $children = $resources->children->children();
        } else {
            $children = $resources->children();
        }
        if (empty($children)) {
            return $item;
        }

        if ($children) {
            $item['children'] = array();
            foreach ($children as $child) {
                if ($child->getName() != 'title' && $child->getName() != 'sort_order' && $child->attributes()->module) {
                    $item['state'] = 'open';
                    $item['children'][] = $this->_mapResources($child);
                }
            }
            if (!empty($item['children'])) {
                usort($item['children'], array($this, '_sortTree'));
            }
        }
        return $item;
    }

    /**
     * Sort tree by sort order
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTree($a, $b)
    {
        return $a['sort_order'] < $b['sort_order'] ? -1 : ($a['sort_order'] > $b['sort_order'] ? 1 : 0);
    }
}
