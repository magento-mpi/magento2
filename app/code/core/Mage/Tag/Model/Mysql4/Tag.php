<?php
class Mage_Tag_Model_Mysql4_Tag {
	protected $_tagTable;
    protected $_tagRelTable;
    protected $_tagEntityTable;
    
    /**
     * Read connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;

    /**
     * Write connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    public function __construct() {
        $resources = Mage::getSingleton('core/resource');
        
        $this->_tagTable         = $resources->getTableName('tag/tag');
        $this->_tagRelTable      = $resources->getTableName('tag/tag_relations');
        $this->_tagEntityTable   = $resources->getTableName('tag/tag_entity');
        
        $this->_read    = $resources->getConnection('tag_read');
        $this->_write   = $resources->getConnection('tag_write');
    }
    
    public function load($tagId) {
        
    }
    
    public function save(Mage_Tag_Model_Tag $tag) {    	
        $this->_write->beginTransaction();
        try {        	
            if ($tag->getId() && 0) {
                $data = $this->_prepareUpdateData($tag);
            } else {            	
            	$sql = "SELECT id 
		    			FROM {$this->_tagTable} 
		    			WHERE tagname = :tag_name";
		        $nid = $this->_read->fetchOne($sql, array('tag_name' => $tag->getTagName()));		        
		        
		        $data = $this->_prepareInsertData($tag);
		        if (empty($nid)) {	                
					$this->_write->insert($this->_tagTable, $data['detail']);
	                
	                $tag->setId($this->_write->lastInsertId());
		        } else {
		        	$tag->setId($nid);
		        }
		        
                $data['base']['tag_id'] = $tag->getId();                
                $this->_write->insert($this->_tagRelTable, $data['base']);
            }
            $this->_write->commit();            
        } catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    /**
     * Prepare data for tag insert
     *
     * @todo    validate data
     * @param   Mage_Tag_Model_Tag $tag
     * @return  array
     */
    protected function _prepareInsertData(Mage_Tag_Model_Tag $tag) {
    	if (is_string($tag->getEntityId())) {
    		$sql = "SELECT id FROM {$this->_tagEntityTable} WHERE title = :title";
    		$id = $this->_read->fetchOne($sql, array('title' => $tag->getEntityId()));
    		$tag->setEntityId($id);
    	}
    	
        $data = array(
            'base'  => array(
                'entity_id'      => $tag->getEntityId(),
                'entity_val_id'  => $tag->getEntityValId()
            ),
            'detail'=> array(
                'tagname'   => $tag->getTagName(),
                'status'    => $tag->getStatus(),
                'store_id'	=> Mage::getSingleton('core/store')->getId()
            )
        );
        
        return $data;
    }
    
    public function _prepareUpdateData(Mage_Tag_Model_Tag $tag) {
        
    }
    
    public function delete(Mage_Tag_Model_Tag $tag) {
    	/*
        $uid = $tag->getUserId();
    	$id = $tag->getTagId();
    	
    	if (!empty($uid)) {
			$condition = $this->_write->quoteInto('tag_id=?', $id).
						 " AND ".
						 $this->_write->quoteInto('user_id=?', $uid);
       		$this->_write->delete($this->_tagRelTable, $condition);
		} else {
			$condition = $this->_write->quoteInto('id=?', $id);
        	$this->_write->delete($this->_tagTable, $condition);
		}
		*/
    }
}
?>