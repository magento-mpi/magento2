<?php
class Mage_Catalog_Model_Mysql4_Tags {
	protected $_relationTable;
    protected $_tagTable;
    protected $_attributeTable;
    
    protected $_read;
    protected $_write;

    public function __construct() {   
    	 	
        $this->_relationTable 		= Mage::getSingleton('core/resource')->getTableName('catalog/product_tags');
        $this->_tagTable 			= Mage::getSingleton('core/resource')->getTableName('catalog/tags');
        $this->_attributeTable      = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute');
        
        $this->_read 				= Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write 				= Mage::getSingleton('core/resource')->getConnection('catalog_write');
    }
    
    public function getProductTags($productId, $uid, $filter) {
    	$max_height = 25;
    	
    	$sql = "SELECT t2.tag_name, 
					   t1.product_id, 
					   t1.user_id,
					   t2.id, 
					   COUNT(*) AS total 
				FROM {$this->_relationTable} AS t1 
				LEFT JOIN {$this->_tagTable} AS t2 ON t2.id = t1.tag_id 
				WHERE t2.status & {$filter}";    	
		if (!empty($productId)) { $sql .= " AND t1.product_id = {$productId} "; }
		if (!empty($uid)) { $sql .= " AND t1.user_id = {$uid} "; }
		
		$sql .= " GROUP BY t1.tag_id";		
		
        $list['data'] = $this->_read->fetchAll($sql);
        $list['total'] = $list['max'] = 0;
        foreach ($list['data'] as $item) {
        	$list['total'] += $item['total'];
        	$list['max'] = $list['max'] < $item['total'] ? $item['total'] : $list['max'];
        }
        
        foreach ($list['data'] as & $item) {
        	$item['size'] = intval($max_height / $list['max'] * $item['total'])."px";
        }
        unset($item); //destroy link to $item variable
        
        return $list;		
    }
    
    public function load(Mage_Catalog_Model_Tags $tags) {
    	$this->getProductTags($tags->getProductId(), $tags->getUserId(), 7);
    }
    
    public function add(Mage_Catalog_Model_Tags $tags) {
    	$sql = "SELECT id, status
    			FROM {$this->_tagTable} 
    			WHERE tag_name=:tag_name";
        $row = $this->_read->fetchRow($sql, array('tag_name'=>$tags->getTagName()));        
        $id = $row['id'];
        if (empty($id)) {
	    	$data = array(
	        	'tag_name'		=> $tags->getTagName(),
	            'status'     	=> 1
	        );                
	        $this->_write->insert($this->_tagTable, $data);        
	        $id = $this->_write->lastInsertId();
        }
        
        $data = array(
        	'user_id'			=> $tags->getUserId(),
            'tag_id'     		=> $id,
            'product_id'    	=> $tags->getProductId()
        );        
        $this->_write->insert($this->_relationTable, $data);
    }
    
    public function delete(Mage_Catalog_Model_Tags $tags) {
    	$uid = $tags->getUserId();
    	$id = $tags->getTagId();
    	
    	if (!empty($uid)) {
			$condition = $this->_write->quoteInto('tag_id=?', $id).
						 " AND ".
						 $this->_write->quoteInto('user_id=?', $uid);
       		$this->_write->delete($this->_relationTable, $condition);
		} else {
			$condition = $this->_write->quoteInto('id=?', $id);
        	$this->_write->delete($this->_tagTable, $condition);
		}
    }
    
    public function update(Mage_Catalog_Model_Tags $tags) {
    	$new_name = $tags->getTagName();
    	$id = $tags->getTagId();
    	
    	$sql = "SELECT id 
    			FROM {$this->_tagTable} 
    			WHERE tag_name=:tag_name";
        $nid = $this->_read->fetchOne($sql, array('tag_name'=>$new_name));        
        if (!empty($nid)) {
	        $data = array(
			    'tag_id'      => $nid
			);
			try {
				$this->_write->update($this->_relationTable, $data, "tag_id = {$id}");
				$this->_write->update($this->_tagTable, array('status' => 1), "id = {$nid}");
			} catch (Exception $e) {
				$this->_write->delete($this->_relationTable, $this->_write->quoteInto('tag_id=?', $id));
			}
			$condition = $this->_write->quoteInto('id=?', $id);
        	$this->_write->delete($this->_tagTable, $condition);
        } else {
        	$data = array(
			    'tag_name'      => $new_name,
			    'status'		=> 1
			);		
			$this->_write->update($this->_tagTable, $data, "id = ".$id);
        }
    }
    
    public function setStatus($tags) {
    	foreach ($tags as $key => $val) {
			$this->_write->update($this->_tagTable, array('status' => $val), "id = {$key}");
		}
    }
    
    public function getTaggedProducts($uid) {
    	$sql = "SELECT t4.attribute_value AS product_name, 
    				   t1.product_id
				FROM {$this->_relationTable} AS t1
				LEFT JOIN {$this->_attributeTable}_varchar AS t4 USING(product_id)
				LEFT JOIN {$this->_attributeTable} AS t3 USING(attribute_id)
				WHERE t3.attribute_code = 'name'
					AND t1.user_id = :uid
				GROUP BY t1.product_id";
    	
    	$list = $this->_read->fetchAll($sql, array('uid' => $uid));
    	
    	return $list;
    }
    
    public function getTaggedCustomers($pid) {
    	$sql = "SELECT t1.firstname
				FROM customer AS t1
				LEFT JOIN {$this->_relationTable} AS t2 ON t1.customer_id = t2.user_id
				WHERE t2.product_id = :product_id
				GROUP BY t1.customer_id";
    	
    	$list = $this->_read->fetchAll($sql, array('product_id' => $pid));
    	
    	return $list;
    }
}

?>