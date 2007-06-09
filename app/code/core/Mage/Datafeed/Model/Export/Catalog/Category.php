<?php
/**
 * Export catalog Categories
 *
 * @package     Mage
 * @subpackage  Datafeed
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Datafeed_Model_Export_Catalog_Category extends Varien_Object 
{
    public function __construct() 
    {
        
    }
    
    public function getCategoriesList($parentId)
    {
        $nodes = Mage::getModel('catalog_resource/category_tree')
            ->joinAttribute('name')
            ->joinAttribute('description')
            ->load($parentId)
            ->getNodes();
         
        return $nodes;
    }
}
