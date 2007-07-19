<?php
/**
 * Categories tree block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tree extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/catalog/category/tree.phtml');
    }
    
    public function getNodesUrl()
    {
        return $this->getUrl('*/catalog_category/jsonTree');
    }
    
    public function getEditUrl()
    {
        return $this->getUrl('*/catalog_category/edit');
    }
}
