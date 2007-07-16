<?php
class Mage_Tag_Model_Mysql4_Tag_Collection extends Mage_Core_Model_Resource_Collection_Abstract
{
	protected $_tagTable;
    protected $_tagRelTable;
    protected $_tagEntityTable;

    public function __construct() {
        $resources = Mage::getSingleton('core/resource');

        parent::__construct($resources->getConnection('tag_read'));

        $this->_tagTable         = $resources->getTableName('tag/tag');
        $this->_tagRelTable   = $resources->getTableName('tag/tag_relations');
        $this->_tagEntityTable   = $resources->getTableName('tag/tag_entity');

        $this->_sqlSelect->from($this->_tagTable, array('*', 'total_used' => 'COUNT(tag_relations_id)'))
            ->joinLeft($this->_tagRelTable, $this->_tagTable.'.tag_id='.$this->_tagRelTable.'.tag_id', 'tag_relations_id')
            ->group($this->_tagRelTable.'.tag_id');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('tag/tag'));
    }

    /**
     * Add store filter
     *
     * @param   int $storeId
     * @return  Varien_Data_Collection_Db
     */
    public function addStoreFilter($storeId) {
        Mage::log('Add store filter to tag collection');
        $this->addFilter('store',
            $this->getConnection()->quoteInto($this->_tagTable.'.store_id=?', $storeId),
            'string');

        return $this;
    }

    /**
     * Add entity filter
     *
     * @param   int|string $entity
     * @param   int $pkValue
     * @return  Varien_Data_Collection_Db
     */
    public function addEntityFilter($entity, $val) {
        Mage::log('Add entity filter to tag collection');
        if (is_numeric($entity)) {
            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_tagRelTable.'.entity_id=?', $entity),
                'string');
        } elseif (is_string($entity)) {
            $this->_sqlSelect->join($this->_tagEntityTable,
                $this->_tagRelTable.'.entity_id='.$this->_tagEntityTable.'.tag_entity_id');

            $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_tagEntityTable.'.title=?', $entity),
                'string');
        }

        $this->addFilter('entity',
                $this->getConnection()->quoteInto($this->_tagRelTable.'.entity_val_id=?', $val),
                'string');

        return $this;
    }

    /**
     * Add status filter
     *
     * @param   int|string $status
     * @return  Varien_Data_Collection_Db
     */
    public function addStatusFilter($status) {
    	Mage::log('Add status filter to tag collection');
        if (is_numeric($status)) {
            $this->addFilter('status',
                $this->getConnection()->quoteInto($this->_tagTable.'.status=?', $status),
                'string');
        }

        return $this;
    }

    public function addSearch($q) {
    	if (empty($q)) return $this;

    	$this->_sqlSelect->where("tagname LIKE '%{$q}%'");

    	return $this;
    }

    public function getFSize($id) {
    	$max = 0;
    	foreach ($this->getItems() as $item) {
    		if ($item->getTotal() > $max) {
    			$max = $item->getTotal();
    		}
    	}

    	foreach ($this->getItems() as $item) {
    		if ($item->getTag_id() != $id) {
    			continue;
    		} else {
    			return ($item->getTotal() / $max * 21)."px";
    		}
    	}
    }
}
?>