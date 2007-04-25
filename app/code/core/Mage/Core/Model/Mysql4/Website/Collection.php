<?php
/**
 * Websites collection
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Website_Collection extends Varien_Data_Collection_Db
{
    protected $_websiteTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('core_read'));
        
        $this->_websiteTable = Mage::registry('resources')->getTableName('core_resource', 'website');
        $this->_sqlSelect->from($this->_websiteTable);
    }

    public function addCategoryFilter($category)
    {
        if (is_array($category)) {
            $condition = $this->getConnection()->quoteInto("root_category_id IN (?)", $category);
        }
        else {
            $condition = $this->getConnection()->quoteInto("root_category_id=?",$category);
        }

        $this->addFilter('category', $condition, 'string');
        return $this;
    }
}