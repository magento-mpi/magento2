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
 * Categories tree block for URL rewrites editing process
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * List of allowed category ids
     *
     * @var array|null
     */
    protected $_allowedCategoryIds = null;

    protected $_template = 'urlrewrite/categories.phtml';

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        parent::__construct($coreData, $context, $registry, $data);
    }

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
        $productId = Mage::app()->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->setId($productId);
            $this->_allowedCategoryIds = $product->getCategoryIds();
            unset($product);
        }

        $result = array();
        if ($parentId) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($parentId);
            if (!empty($category)) {
                $tree = $this->_getNodesArray($this->getNode($category, $recursionLevel));
                if (!empty($tree) && !empty($tree['children'])) {
                    $result = $tree['children'];
                }
            }
        } else {
            $result = $this->_getNodesArray($this->getRoot(null, $recursionLevel));
        }

        if ($asJson) {
            return $this->_coreData->jsonEncode($result);
        }

        $this->_allowedCategoryIds = null;

        return $result;
    }

    /**
     * Get categories collection
     *
     * @return Magento_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->_getData('category_collection');
        if (is_null($collection)) {
            $collection = Mage::getModel('Magento_Catalog_Model_Category')->getCollection()
                ->addAttributeToSelect(array('name', 'is_active'))
                ->setLoadProductCount(true);
            $this->setData('category_collection', $collection);
        }

        return $collection;
    }

    /**
     * Convert categories tree to array recursively
     *
     * @param  Magento_Data_Tree_Node $node
     * @return array
     */
    protected function _getNodesArray($node)
    {
        $result = array(
            'id'             => (int)$node->getId(),
            'parent_id'      => (int)$node->getParentId(),
            'children_count' => (int)$node->getChildrenCount(),
            'is_active'      => (bool)$node->getIsActive(),
            'name'           => $node->getName(),
            'level'          => (int)$node->getLevel(),
            'product_count'  => (int)$node->getProductCount()
        );

        if (is_array($this->_allowedCategoryIds) && !in_array($result['id'], $this->_allowedCategoryIds)) {
            $result['disabled'] = true;
        }

        if ($node->hasChildren()) {
            $result['children'] = array();
            foreach ($node->getChildren() as $childNode) {
                $result['children'][] = $this->_getNodesArray($childNode);
            }
        }
        $result['cls']      = ($result['is_active'] ? '' : 'no-') . 'active-category';
        $result['expanded'] = (!empty($result['children']));

        return $result;
    }

    /**
     * Get URL for categories tree ajax loader
     *
     * @return string
     */
    public function getLoadTreeUrl()
    {
        return $this->_adminhtmlData->getUrl('*/*/categoriesJson');
    }
}
