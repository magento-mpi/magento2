<?php
/**
 * Catalog category
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category extends Varien_Data_Object
{
    public function __construct($category=false) 
    {
        parent::__construct();
        
        if (is_numeric($category)) {
            $this->load($category);
        }
        elseif (is_array($category)) {
            $this->setData($category);
        }
    }
    
    public function getProducts()
    {
        if ($this->getCategoryId()) {
            
        }
    }
}