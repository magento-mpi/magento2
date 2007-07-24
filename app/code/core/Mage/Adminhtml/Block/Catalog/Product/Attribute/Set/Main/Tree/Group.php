<?php
/**
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Set_Main_Tree_Group extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('catalog/product/attribute/set/main/tree/group.phtml');
    }
}