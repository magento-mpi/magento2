<?php

include_once('Mage/Catalog/Model/Mysql4.php');

/**
 * Category attribute abstract model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Catalog_Model_Mysql4_Category_Attribute_Abstract extends Mage_Catalog_Model_Mysql4 implements Mage_Core_Model_Db_Table_Interface 
{
    public function __construct() 
    {
        
    }
    
    function changeSaver()
    {
        
    }
}