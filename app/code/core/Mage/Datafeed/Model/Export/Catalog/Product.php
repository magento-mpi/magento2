<?php
/**
 * Export catalog product
 *
 * @package     Mage
 * @subpackage  Datafeed
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Datafeed_Model_Export_Catalog_Product extends Varien_Object 
{
    public function __construct() 
    {
        
    }
    
    public function getCategoryProducts($categoryId, $count=10)
    {
        $collection = Mage::getModel('catalog_resource', 'product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addCategoryFilter($categoryId)
            ->setPageSize($count)
            ->setOrder('create_date', 'desc')
            ->load();
        return $collection;
    }
}
