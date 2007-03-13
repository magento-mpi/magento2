<?php
/**
 * Catalog product and category attributes controller
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_AttributesController extends Mage_Core_Controller_Admin_Action
{
    public function indexAction()
    {
        
    }
    
    public function treeAction()
    {
        Mage_Core_Block::loadJsonFile('Mage/Catalog/Admin/attributes/tree.json', 'mage_catalog');
    }
}