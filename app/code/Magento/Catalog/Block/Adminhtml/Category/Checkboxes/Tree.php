<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Categories tree with checkboxes
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Category\Checkboxes;

use Magento\Framework\Data\Tree\Node;

class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * @var int[]
     */
    protected $_selectedIds = array();

    /**
     * @var array
     */
    protected $_expandedPath = array();

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('catalog/category/checkboxes/tree.phtml');
    }

    /**
     * @return int[]
     */
    public function getCategoryIds()
    {
        return $this->_selectedIds;
    }

    /**
     * @param mixed $ids
     * @return $this
     */
    public function setCategoryIds($ids)
    {
        if (empty($ids)) {
            $ids = array();
        } elseif (!is_array($ids)) {
            $ids = array((int)$ids);
        }
        $this->_selectedIds = $ids;
        return $this;
    }

    /**
     * @return array
     */
    protected function getExpandedPath()
    {
        return array_unique($this->_expandedPath);
    }

    /**
     * @param array $path
     */
    protected function setExpandedPath($path)
    {
        $this->_expandedPath = array_merge($this->_expandedPath, explode('/', $path));
    }

    /**
     * @param array|Node $node
     * @param int $level
     * @return array
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = array();
        $item['text'] = $this->escapeHtml($node->getName());
        if ($this->_withProductCount) {
            $item['text'] .= ' (' . $node->getProductCount() . ')';
        }
        $item['id'] = $node->getId();
        $item['path'] = $node->getData('path');
        $item['cls'] = 'folder ' . ($node->getIsActive() ? 'active-category' : 'no-active-category');
        $item['allowDrop'] = false;
        $item['allowDrag'] = false;
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $this->setExpandedPath($node->getData('path'));
            $item['checked'] = true;
        }
        if ($node->getLevel() < 2) {
            $this->setExpandedPath($node->getData('path'));
        }
        if ($node->hasChildren()) {
            $item['children'] = array();
            foreach ($node->getChildren() as $child) {
                $item['children'][] = $this->_getNodeJson($child, $level + 1);
            }
        }
        if (empty($item['children']) && (int)$node->getChildrenCount() > 0) {
            $item['children'] = array();
        }
        $item['expanded'] = in_array($node->getId(), $this->getExpandedPath());
        return $item;
    }
}
