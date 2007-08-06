<?php
/**
 * Catalog product tier price backend attribute model
 *
 * @package    Mage
 * @subpackage Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Catalog_Model_Entity_Product_Attribute_Backend_Tierprice extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	/**
	 * DB connections list
	 *
	 * @var array
	 */
	protected $_connections = array();
	
	/**
	 * Attribute main table
	 *
	 * @var string
	 */
	protected $_mainTable = null;
		
	public function getMainTable() 
	{
		if (is_null($this->_mainTable)) {
			$this->_mainTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_tier_price');
		}
		
		return $this->_mainTable;
	}
	
	public function afterLoad($object)
    {
    	$storeId = $object->getStoreId();
                
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityIdField = $this->getEntityIdField();
     	
        $select = $this->getConnection('read')->select()
        	->from($this->getMainTable(), array('qty AS price_qty', 'value AS price'))
        	->where('store_id = ?', $storeId)
        	->where($entityIdField . ' = ?', $entityId)
        	->where('attribute_id = ?', $attributeId);
        	
        $object->setData($this->getAttribute()->getName(), $this->getConnection('read')->fetchAll($select));
    }
    
    public function beforeSave($object)
    {
    
    }
    
    public function afterSave($object) 
    {
    	$storeId = $object->getStoreId();
                
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityTypeId  = $this->getAttribute()->getEntity()->getTypeId();
        $entityIdField = $this->getEntityIdField();
        
        $connection = $this->getConnection('write');
        
    	$condition = array(
    		$connection->quoteInto('store_id = ?', $storeId),
    		$connection->quoteInto($entityIdField . ' = ?', $entityId),
    		$connection->quoteInto('attribute_id = ?', $attributeId)
    	);
    	    	 
    	$connection->delete($this->getMainTable(), $condition);
    	
    	$tierPrices = $object->getData($this->getAttribute()->getName());
    	
    	if (!is_array($tierPrices)) {
    		return;
    	}
    	
    	$finalValue = array();
    	
    	foreach ($tierPrices as $tierPrice) {
    		if (!isset($tierPrice['price_qty']) || !isset($tierPrice['price']) || strlen($storeId)==0) {
    			continue;
    		}
    		
    		$data = array();
    		$data[$entityIdField] 	= $entityId;
    		$data['attribute_id'] 	= $attributeId;
    		$data['store_id']	  	= $storeId;
    		$data['qty']		  	= $tierPrice['price_qty'];
    		$data['value']		  	= $tierPrice['price'];
    		$data['entity_type_id'] = $entityTypeId;
    		
    		$connection->insert($this->getMainTable(), $data);
    	}
    }
    
    public function afterDelete($object) 
    {
    	if ($object->getUseDataSharing()) {
            $storeId = $object->getData('store_id');
        } else {
            $storeId = $object->getStoreId();
        }
        
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityTypeId  = $object->getTypeId();
        $entityIdField = $this->getEntityIdField();
        
        $connection = $this->getConnection('write');
        
    	$condition = array(
    		$connection->quoteInto('store_id = ?', $storeId),
    		$connection->quoteInto($entityIdField . ' = ?', $entityId),
    		$connection->quoteInto('attribute_id = ?', $attributeId)
    	);
    	    	 
    	$connection->delete($this->getMainTable(), $condition);    	
    }
    
    /**
     * Return DB connection
     *
     * @param	string		$type
     * @return	Zend_Db_Adapter_Abstract
     */    
    public function getConnection($type)
    {
    	if (!isset($this->_connections[$type])) {
    		$this->_connections[$type] = Mage::getSingleton('core/resource')->getConnection('catalog_' . $type);
    	}
    	
    	return $this->_connections[$type];
    }
    
}// Class Mage_Catalog_Model_Entity_Product_Attribute_Backend_Tierprice END