<?php
/**
 * Category attribute saver resource
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category_Attribute_Saver
{
    protected $_write;
    
    public function __construct() 
    {
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
    }
    
    public function save(Mage_Catalog_Model_Category_Attribute $attribute, $categoryId, $value)
    {
        $table = $attribute->getTableName();
        $storeId = Mage::getSingleton('core/store')->getId();
        $attributeId = $attribute->getId();
        
        $condition = $this->_write->quoteInto('category_id=?',$categoryId)
                     . ' AND ' . $this->_write->quoteInto('attribute_id=?',$attributeId)
                     . ' AND ' . $this->_write->quoteInto('store_id=?',$storeId);
        
        try {
            $this->_write->delete($table, $condition);
            
            $data = array(
                'category_id'   => $categoryId,
                'attribute_id'  => $attributeId,
                'store_id'    => $storeId
            );
            
            if (is_array($value)) {
                foreach ($value as $val) {
                    $data['attribute_value'] = $val;
                    $this->_write->insert($table, $data);
                }
            }
            else {
                $data['attribute_value'] = $value;
                $this->_write->insert($table, $data);
            }
        }
        catch (Exception $e){
            throw new Exception('Attribute "'.$attribute->getCode().'" value save error: '.$e->getMessage());
        }
        
        return $this;
    }
}