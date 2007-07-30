<?php
/**
 * Category image attribute backend
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Backend_Image extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
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
			$this->_mainTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_image');
		}
		
		return $this->_mainTable;
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

	public function afterLoad($object)
    {
    	$storeId = $object->getStoreId();
                
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityIdField = $this->getEntityIdField();
     	
        $select = $this->getConnection('read')->select()
        	->from($this->getMainTable(), array('value_id AS id', 'value AS image', 'position AS position'))
        	->where('store_id = ?', $storeId)
        	->where($entityIdField . ' = ?', $entityId)
        	->where('attribute_id = ?', $attributeId)
            ->order('position', 'asc');
        	
        $object->setData($this->getAttribute()->getName(), $this->getConnection('read')->fetchAll($select));
    }

    public function afterSave($object)
    {
        try {
            $uploader = new Varien_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
        }
        catch (Exception $e){
            return;
        }
        
        //$uploader->save($this->getAttribute()->getEntity()->getStore()->getConfig('system/filesystem/upload'));
        $uploader->save(Mage::getSingleton('core/store')->getConfig('system/filesystem/upload'));
        
    	$storeId       = $object->getStoreId();
        $attributeId   = $this->getAttribute()->getId();
        $entityId	   = $object->getId();
        $entityIdField = $this->getEntityIdField();
        $entityTypeId  = $this->getAttribute()->getEntity()->getTypeId();
        
        $connection = $this->getConnection('write');
        
        $values = $object->getData($this->getAttribute()->getName());

        if(isset($values['delete']))
        {
            foreach ((array)$values['delete'] as $value_id) {
    	        $condition = array(
    		        $connection->quoteInto('value_id = ?', $value_id)
    	        );
    	        $connection->delete($this->getMainTable(), $condition);
    	    }
        }

        if(isset($values['position']))
        {
            foreach ((array)$values['position'] as $value_id => $position) {
    	        $condition = array(
    		        $connection->quoteInto('value_id = ?', $value_id)
    	        );
                $data = array();
                $data['position'] = $position;
    	        $connection->update($this->getMainTable(), $data, $condition);
    	    }
        }

        $i = 0;
    	foreach ((array)$uploader->getUploadedFileName() as $uploadedFileName) {
    		if ($uploadedFileName == '') {
    			continue;
    		}
    		
    		$data = array();
    		$data[$entityIdField] 	= $entityId;
    		$data['attribute_id'] 	= $attributeId;
    		$data['store_id']	  	= $storeId;
    		$data['position']		= (isset($values['position_new'][$i])?$values['position_new'][$i]:0);
    		$data['value']		  	= $uploadedFileName;
    		$data['entity_type_id'] = $entityTypeId;
    		
    		$connection->insert($this->getMainTable(), $data);
            $i++;
    	}
    }
}
