<?php
class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

}

__halt_compiler();

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
            	$sql = "SELECT tag_id
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

    private function _getEntityId($eid) {
    	if (is_string($eid)) {
    		$sql = "SELECT tag_entity_id FROM {$this->_tagEntityTable} WHERE title = :title";
    		$id = $this->_read->fetchOne($sql, array('title' => $eid));
    	}

    	return $id;
    }

    /**
     * Prepare data for tag insert
     *
     * @todo    validate data
     * @param   Mage_Tag_Model_Tag $tag
     * @return  array
     */
    protected function _prepareInsertData(Mage_Tag_Model_Tag $tag) {
    	$tag->setEntityId($this->_getEntityId($tag->getEntityId()));

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

    public function update(Mage_Tag_Model_Tag $tag) {
    	$sql = "SELECT tag_id
				FROM {$this->_tagTable}
				WHERE tagname = :tag_name";
		$nid = $this->_read->fetchOne($sql, array('tag_name' => $tag->getTagName()));
		if (!empty($nid)) {
			$sql = "UPDATE IGNORE {$this->_tagRelTable}
					SET tag_id = {$nid}
					WHERE tag_id = ".$tag->getId();
			$result = $this->_write->query($sql);

			$this->_write->delete($this->_tagTable, 'tag_id = '.$tag->getId());
			$this->_write->delete($this->_tagRelTable, 'tag_id = '.$tag->getId());
		} else {
			$data = array();

			if ($tag->getStatus()) {
				$data['status'] = $tag->getStatus();
			}

			if ($tag->getTagName()) {
				$data['tagname'] = $tag->getTagName();
			}

			$result = $this->_write->update($this->_tagTable, $data, 'tag_id = '.$tag->getId());
		}
    }

    public function delete(Mage_Tag_Model_Tag $tag) {
    	$tag->setEntityId($this->_getEntityId($tag->getEntityId()));

	    $condition = $this->_write->quoteInto('tag_id=?', $tag->getId()).
	    	" AND ".
	    	$this->_write->quoteInto('entity_val_id=?', $tag->getEntityValId()).
	    	" AND ".
	    	$this->_write->quoteInto('entity_id=?', $tag->getEntityId());
  		$this->_write->delete($this->_tagRelTable, $condition);
    }
}
?>