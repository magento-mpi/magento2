<?php
/**
 * Product categories tab
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Category_Tree
{
    protected $_categoryIds;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/categories.phtml');
    }
    
    protected function getCategoryIds()
    {
        if (is_null($this->_categoryIds)) {
            $this->_categoryIds = array();
            $collection = Mage::registry('product')->getCategoryCollection()
                ->load();
            foreach ($collection as $category) {
            	$this->_categoryIds[] = $category->getId();
            }
        }
        return $this->_categoryIds;
    }
    
    public function getRootNode()
    {
        $root = parent::getRootNode();
        if (in_array($root->getId(), $this->getCategoryIds())) {
            $root->setChecked(true);
        }
        return $root;
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = parent::_getNodeJson($node, $level);
        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }
        return $item;
    }
}
