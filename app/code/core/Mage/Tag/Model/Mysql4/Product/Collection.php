<?php
/**
 * Tagged products Collection
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Tag_Model_Mysql4_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
 	protected $_entitiesAlias = array();
 	protected $_customerFilterId;

	public function __construct()
	{
	    parent::__construct();
        $this->getSelect()->group('e.entity_id');
	}

    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where('t.store_id = ?', $storeId);
        return $this;
    }

	public function addCustomerFilter($customerId)
	{
        $this->getSelect()
            ->where('tr.customer_id = ?', $customerId);
        $this->_customerFilterId = $customerId;
		return $this;
	}

	public function addTagFilter($tagId)
	{
        $this->getSelect()
            ->where('tr.tag_id = ?', $tagId);
		return $this;
	}

	public function addStatusFilter($status)
	{
        $this->getSelect()
            ->where('t.status = ?', $status);
		return $this;
	}

    public function setDescOrder($dir='DESC')
    {
        $this->getSelect()
            ->order('tr.tag_relation_id', $dir);
        return $this;
    }

    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    public function addPopularity($tagId)
    {
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');

        $this->getSelect()
            ->joinLeft(array('tr2' => $tagRelationTable), 'tr2.product_id=e.entity_id', array('popularity' => 'COUNT(DISTINCT tr2.tag_relation_id)'))
            ->where('tr2.tag_id = ?', $tagId);
        return $this;
    }

    public function addProductTags()
    {
        foreach( $this->getItems() as $item ) {
            $tagsCollection = Mage::getModel('tag/tag')
                ->getResourceCollection()
                ->addPopularity()
                ->addProductFilter($item->getEntityId())
                ->addCustomerFilter($this->_customerFilterId)
                ->load();
            $item->setProductTags( $tagsCollection );
        }

        return $this;
    }

    protected function _joinFields()
    {
        $tagTable = Mage::getSingleton('core/resource')->getTableName('tag/tag');
        $tagRelationTable = Mage::getSingleton('core/resource')->getTableName('tag/relation');

        $this->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('small_image');

        $this->getSelect()
            ->join(array('tr' => $tagRelationTable), "tr.product_id = e.entity_id")
            ->join(array('t' => $tagTable), "t.tag_id = tr.tag_id");
    }

    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();

        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(DISTINCT e.entity_id) from ', $sql);
        return $sql;
    }

    /**
     * Load entities records into items
     *
     * @link Mage_Catalog_Model_Entity_Product_Bundle_Option_Link_Collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadEntities($printQuery = false, $logQuery = false)
    {
        $entity = $this->getEntity();
        $entityIdField = $entity->getEntityIdField();

        if ($this->_pageSize) {
            $this->getSelect()->limitPage($this->_getPageStart(), $this->_pageSize);
        }

        $this->printLogQuery($printQuery, $logQuery);

        $rows = $this->_read->fetchAll($this->getSelect());
        if (!$rows) {
            return $this;
        }

        foreach ($rows as $v) {
            $object = clone $this->getObject();
            if(!isset($this->_entitiesAlias[$v[$entityIdField]])) {
            	$this->_entitiesAlias[$v[$entityIdField]] = array();
            }
            $this->_items[] = $object->setData($v);
            $this->_entitiesAlias[$v[$entityIdField]][] = sizeof($this->_items)-1;
        }
        return $this;
    }

    protected function _getEntityAlias($entityId)
    {
    	if(isset($this->_entitiesAlias[$entityId])) {
    		return $this->_entitiesAlias[$entityId];
    	}

    	return false;
    }

    /**
     * Load attributes into loaded entities
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if (empty($this->_items) || empty($this->_selectAttributes)) {
            return $this;
        }

        $entity = $this->getEntity();
        $entityIdField = $entity->getEntityIdField();

        $condition = "entity_type_id=".$entity->getTypeId();
        $condition .= " and ".$this->_read->quoteInto("$entityIdField in (?)", array_keys($this->_entitiesAlias));
        $condition .= " and ".$this->_read->quoteInto("store_id in (?)", $entity->getSharedStoreIds());
        $condition .= " and ".$this->_read->quoteInto("attribute_id in (?)", $this->_selectAttributes);

        $attrById = array();
        foreach ($entity->getAttributesByTable() as $table=>$attributes) {
            $sql = "select $entityIdField, attribute_id, value from $table where $condition";
            $this->printLogQuery($printQuery, $logQuery, $sql);
            $values = $this->_read->fetchAll($sql);
            if (empty($values)) {
                continue;
            }
            foreach ($values as $v) {
                if (!$this->_getEntityAlias($v[$entityIdField])) {
                    throw Mage::exception('Mage_Eav', 'Data integrity: No header row found for attribute');
                }
                if (!isset($attrById[$v['attribute_id']])) {
                    $attrById[$v['attribute_id']] = $entity->getAttribute($v['attribute_id'])->getAttributeCode();
                }
                foreach ($this->_getEntityAlias($v[$entityIdField]) as $_entityIndex) {
                	$this->_items[$_entityIndex]->setData($attrById[$v['attribute_id']], $v['value']);
                }
            }
        }
        return $this;
    }

    public function setOrder($attribute, $dir='desc')
    {
        if ($attribute == 'popularity') {
            $this->getSelect()->order($attribute . ' ' . $dir);
        }
        else {
        	parent::setOrder($attribute, $dir);
        }
        return $this;
    }
}