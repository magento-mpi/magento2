<?php
/**
 * Product attribute saver resource
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Attribute_Saver
{
    protected $_write;
    
    public function __construct() 
    {
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function save(Mage_Catalog_Model_Product_Attribute $attribute, $productId, $value)
    {
        $table = $attribute->getTableName();
        $websiteId = Mage::registry('website')->getId();
        $attributeId = $attribute->getId();
        
        $condition = $this->_write->quoteInto('product_id=?',$productId)
                     . ' AND ' . $this->_write->quoteInto('attribute_id=?',$attributeId)
                     . ' AND ' . $this->_write->quoteInto('website_id=?',$websiteId);
        
        try {
            $this->_write->delete($table, $condition);
            
            $data = array(
                'product_id'    => $productId,
                'attribute_id'  => $attributeId,
                'website_id'    => $websiteId
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