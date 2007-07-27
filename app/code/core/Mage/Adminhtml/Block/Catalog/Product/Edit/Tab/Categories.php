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
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/edit/categories.phtml');
    }
    
    protected function _getNodeJson($node, $level=1)
    {
        $item = parent::_getNodeJson($node, $level);
        return $item;
    }
}
