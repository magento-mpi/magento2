<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form select categories
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
namespace Magento\CatalogEvent\Block\Adminhtml\Event\Edit;

class Category extends \Magento\Adminhtml\Block\Catalog\Category\AbstractCategory
{
    protected $_template = 'categories.phtml';

    /**
     * Get categories tree as recursive array
     *
     * @param int $parentId
     * @param bool $asJson
     * @param int $recursionLevel
     * @return array
     */
    public function getTreeArray($parentId = null, $asJson = false, $recursionLevel = 3)
    {
        $result = array();
        if ($parentId) {
            $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($parentId);
            if (!empty($category)) {
                $tree = $this->_getNodesArray($this->getNode($category, $recursionLevel));
                if (!empty($tree) && !empty($tree['children'])) {
                    $result = $tree['children'];
                }
            }
        }
        else {
            $result = $this->_getNodesArray($this->getRoot(null, $recursionLevel));
        }
        if ($asJson) {
            return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode($result);
        }
        return $result;
    }

    /**
     * Get categories collection
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->_getData('category_collection');
        if (is_null($collection)) {
            $collection = \Mage::getModel('Magento\Catalog\Model\Category')->getCollection()
                ->addAttributeToSelect(array('name', 'is_active'))
                ->setLoadProductCount(true)
            ;
            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    /**
     * Convert categories tree to array recursively
     *
     * @return array
     */
    protected function _getNodesArray($node)
    {
        $eventHelper = $this->helper('Magento\CatalogEvent\Helper\Adminhtml\Event');
        $result = array(
            'id'             => (int)$node->getId(),
            'parent_id'      => (int)$node->getParentId(),
            'children_count' => (int)$node->getChildrenCount(),
            'is_active'      => (bool)$node->getIsActive(),
            'disabled'       => ($node->getLevel() <= 1 || in_array(
                                    $node->getId(),
                                    $eventHelper->getInEventCategoryIds()
                                )),
            'name'           => $node->getName(),
            'level'          => (int)$node->getLevel(),
            'product_count'  => (int)$node->getProductCount(),
        );
        if ($node->hasChildren()) {
            $result['children'] = array();
            foreach ($node->getChildren() as $childNode) {
                $result['children'][] = $this->_getNodesArray($childNode);
            }
        }
        $result['cls'] = ($result['is_active'] ? '' : 'no-') . 'active-category';
        if ($result['disabled']) {
            $result['cls'] .= ' em';
        }
        $result['expanded'] = false;
        if (!empty($result['children'])) {
            $result['expanded'] = true;
        }
        return $result;
    }

    /**
     * Get URL for categories tree ajax loader
     *
     * @return string
     */
    public function getLoadTreeUrl()
    {
        return $this->getUrl('*/*/categoriesJson');
    }
}
