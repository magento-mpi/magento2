<?php
/**
 * Review Product Collection
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Model_Mysql4_Review_Product_Collection extends Mage_Catalog_Model_Entity_Product_Collection
{
 	protected $_entitiesAlias = array();

	public function __construct()
	{
		$this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('review/review');
	}

    public function addStoreFilter($storeId)
    {
        $this->getSelect()
            ->where('e.store_id = ?', $storeId);
        return $this;
    }

	public function addCustomerFilter($customerId)
	{
        $this->getSelect()
            ->where('rdt.customer_id = ?', $customerId);
		return $this;
	}

    public function setDateOrder($dir='DESC')
    {
        $this->getSelect()
            ->order('rt.created_at', $dir);
        return $this;
    }

    public function addReviewSummary()
    {
        foreach( $this->getItems() as $item ) {
            $model = Mage::getModel('rating/rating');
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    public function addRateVotes()
    {
        foreach( $this->getItems() as $item ) {
            $votesCollection = Mage::getModel('rating/rating_option_vote')
                ->getResourceCollection()
                ->setEntityPkFilter($item->getEntityId())
                ->load();
            $item->setRatingVotes( $votesCollection );
        }
        return $this;
    }

    public function resetSelect()
    {
        parent::resetSelect();
        $this->_joinFields();
        return $this;
    }

    protected function _joinFields()
    {
        $reviewTable = Mage::getSingleton('core/resource')->getTableName('review/review');
        $reviewDetailTable = Mage::getSingleton('core/resource')->getTableName('review/review_detail');

        $this->addAttributeToSelect('name');

        $this->getSelect()
            ->join(array('rt' => $reviewTable), "rt.entity_pk_value = e.entity_id", array('review_id', 'created_at', 'entity_pk_value', 'status_id'))
            ->join(array('rdt' => $reviewDetailTable), "rdt.review_id = rt.review_id");
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
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(e.entity_id) from ', $sql);
        return $sql;
    }

    /**
     * Load entities records into items
     *
     * @link Mage_Catalog_Model_Entity_Product_Bundle_Option_Link_Collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     * @author Ivan Chepurnyi <mitch@varien.com>
     * @thanks ^.^
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

    /*
    public function load($a=false, $b=false)
    {
        echo "debug: <pre>";
        parent::load(1);
        echo "</pre>";
    }
    */
 }