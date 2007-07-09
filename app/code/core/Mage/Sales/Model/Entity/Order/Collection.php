<?php
/**
 * Orders collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Sales_Model_Entity_Order_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('sales/order'));
        $this->setObject('sales/order');
    }

    protected $_joinedEntities = array();
    protected $_joinedAttributes = array();
    protected $_joinedTables = array();

    /**
     * Enter description here...
     *
     * @param unknown_type $attribute
     * @param unknown_type $bind
     * @param unknown_type $filter
     * @param unknown_type $sort
     * @param unknown_type $limit
     * @param unknown_type $aggregate
     * @return unknown
     */
    public function addAttribute($attribute, $bind='', $filter='', $sort='', $limit='', $aggregate='')
    {
        if (is_array($attribute)) {
            list($alias,$attribute) = each($attribute);
        }

        $attribute = $this->_validateAttribute($attribute);
        $entity = $attribute->getEntity();

        if (empty($alias)) {
            $alias = $attribute->getAlias($this->getEntity());
        }

        $this->_addJoinedEntity($entity, $bind);

        if ($entity->getType() !== $this->getEntity()->getType()) {
            if (! empty($filter)) {
                foreach ($filter as $addAttribute => $condition) {
                    $addAttribute = $this->_validateAttribute($addAttribute, $entity);
                    $this->_addJoinedAttribute($addAttribute, $bind, $filter, $sort, $limit, $aggregate);
                }
            }
            if (! empty($bind)) {
                $bind = $this->_validateAttribute($bind, $this->getEntity());
                $this->_addJoinedAttribute($bind, $bind, $filter, $sort, $limit, $aggregate);
            }
            if ($attribute->getBackend()->isStatic()) {
                $this->_joinedEntities[$entity->getType()]['staticAttributes'][$alias] = $attribute->getName();
            }
        }

        $this->_addSelectedAttribute($attribute, $bind, '', $sort, $limit, $aggregate);

        return $this;
    }

    public function joinTable($tableName, $fields, $bind, $cond=null, $joinType='inner')
    {
        // validate table
        if (false !== strpos($tableName, '/')) {
            $table = Mage::getSingleton('core/resource')->getTableName($tableName);
        } else {
            $table = $tableName;
        }
        $this->_joinedTables[$table] = array(
            'tableName' => $tableName,
            'fields' => $fields,
            'bind' => $bind,
            'cond' => $cond,
            'joinType' => $joinType,
        );
        foreach ($bind as $key => $attribute) {
            $attribute = $this->_validateAttribute($attribute);
            $this->_addJoinedAttribute($attribute);
        }
        return $this;
    }

    protected function _addJoinedEntity(Mage_Eav_Model_Entity_Abstract $entity, $bind='') {
        if ($entity->getType() !== $this->getEntity()->getType()) {
            if (isset($this->_joinedEntities[$entity->getType()])) {
                if (! empty($bind)) $this->_joinedEntities[$entity->getType()]['bind'] = $bind;
            } else {
                $this->_joinedEntities[$entity->getType()] = array(
                    'entity' => $entity,
                    'bind' => $bind,
                    'staticAttributes' => array(),
                );
            }
        }
    }

    protected function _addSelectedAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $bind='', $filter='', $sort='', $limit='', $aggregate='') {
        // TODO
        $this->_addJoinedAttribute($attribute, $bind, $filter, $sort, $limit, $aggregate);
    }

    protected function _addJoinedAttribute(Mage_Eav_Model_Entity_Attribute_Abstract $attribute, $bind='', $filter='', $sort='', $limit='', $aggregate='') {
        $this->_addJoinedEntity($attribute->getEntity(), $bind);
        if (! isset($this->_joinedAttributes[$attribute->getAlias($this->getEntity())])) {
            $this->_joinedAttributes[$attribute->getAlias($this->getEntity())] = array(
                'attribute' => $attribute,
                'filter' => array( $filter ),
                'sort' => $sort,
                'limit' => $limit,
                'aggregate' => $aggregate,
            );
        } else {
            $this->_joinedAttributes[$attribute->getAlias($this->getEntity())]['filter'][] = $filter;
        }
    }

    /**
     * Validate attribute
     *
     * @param string|Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _validateAttribute($attribute, $entity=null)
    {
        // try to explode combined entity/attribute if supplied
        if (is_string($attribute)) {
            if (false !== strstr($attribute, '/')) {
                $attrArr = explode('/', $attribute);
                if (count($attrArr) > 1) {
                    $entity = $attrArr[0];
                    $attribute = $attrArr[1];
                }
            }
        }

        // validate entity
        if ($entity instanceof Mage_Eav_Model_Entity_Abstract) {
            // entity is valid entity
        } elseif (empty($entity) && ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract)) {
            $entity = $attribute->getEntity();
        } elseif (empty($entity)) {
            $entity = $this->getEntity();
        } else {
            $entity = Mage::getModel('eav/entity')->setType($entity);
        }

        // validate attribute
        if (is_string($attribute)) {
            $attribute = $entity->getAttribute($attribute);
        }
        if (!$attribute) {
            throw Mage::exception('Mage_Eav', 'Invalid attribute type');
        }

        // validate entity
        if (!$entity || !$entity->getTypeId()) {
            throw Mage::exception('Mage_Eav', 'Invalid entity type');
        }

        return $attribute;
    }

//    public function addFilter($attribute, $filter) {
//        $this->addAttribute($attribute, '', $filter);
//        return $this;
//    }

    protected function _loadAll()
    {
        $this->getSelect()->reset();
        $this->getSelect()->where($this->_read->quoteInto('e.entity_type_id=?', $this->getEntity()->getTypeId()));

        $this->getSelect()->from(array('e' => $this->getEntity()->getEntityTable()), '*');

        // build entities and static attributes
        foreach ($this->_joinedEntities as $ent) {
            $entity = $ent['entity'];
            $condition = '(' . $this->_read->quoteIdentifier('_entity_' . $entity->getType() ) . '.' . $this->_read->quoteIdentifier('parent_id') . ' = ' . $this->_read->quoteIdentifier('e') . '.' . $this->getEntity()->getEntityIdField()
                . ') AND ('
                . $this->_read->quoteIdentifier('_entity_' . $entity->getType() ) . '.entity_type_id = ' . $entity->getTypeId()
                . ')';
            if (! empty($ent['bind'])) {
                $this->getSelect()->where(
                    $this->_read->quoteIdentifier('_entity_' . $entity->getType() ) . '.' . $this->_read->quoteIdentifier($entity->getEntityIdField()) . ' = ' . $this->_read->quoteIdentifier('_attribute_' . $ent['bind']->getAlias($this->getEntity())) . '.' . $this->_read->quoteIdentifier('value')
                    );
            }
            $this->getSelect()->joinLeft(
                array('_entity_' . $entity->getType() => $entity->getEntityTable()),
                $condition,
                $ent['staticAttributes']
            );
        }

        // build joined attributes
        foreach ($this->_joinedAttributes as $alias => $attr) {
            $attribute = $attr['attribute'];
            $entity = $attribute->getEntity();
            if (! $attribute->getBackend()->isStatic()) {
                $condition = '((' . $this->_read->quoteIdentifier('_attribute_' . $alias ) . '.' . $this->_read->quoteIdentifier( $attribute->getEntityIdField() ) . ' = ' . $this->_read->quoteIdentifier(($entity->getType() !== $this->getEntity()->getType()) ? '_entity_' . $entity->getType() : 'e') . '.' . $this->_read->quoteIdentifier( $entity->getEntityIdField() )
                    . ') AND (' .
                    $this->_read->quoteIdentifier('_attribute_' . $alias) . '.' . $this->_read->quoteIdentifier('attribute_id') . ' = ' . $attribute->getId() . ')';
                if (! empty($attr['filter'])) {

                }
                $condition .= ')';
                #echo 'Mage_Eav_Model_Entity_Collection_Abstract->_loadAll : ' . $alias . "\n<br>\n";
                $this->getSelect()->joinLeft(
                    array('_attribute_' . $alias  => $attribute->getBackend()->getTable()),
                    $condition,
                    array($alias => 'value')
                );
                if (! empty($attr['filter'])) {
                    foreach ($attr['filter'] as $filter) {
                        if (! empty($filter)) {
                            $this->getSelect()->where($this->_getConditionSql( $this->_read->quoteIdentifier('_attribute_' . $alias) . '.value', $filter));
                        }
                    }
                }
            }
        }

        // build joined tables
        foreach ($this->_joinedTables as $table => $t) {
            $tableName = $t['tableName'];
            $condArr = array();
            foreach ($t['bind'] as $key => $attr) {
                $attribute = $this->_validateAttribute($attr);
                $condArr[] = '(' . $this->_read->quoteIdentifier('_attribute_' . $attribute->getAlias($this->getEntity()) ) . '.value = ' . $this->_read->quoteIdentifier('_joined_' . $tableName) . '.' . $key . ')';
            }
            $fields = array();
            if (is_string($t['fields'])) {
                $fields[$tableName . '/' . $t['fields']] = $t['fields'];
            } elseif (is_array($t['fields'])) {
                foreach ($t['fields'] as $field) {
                    $fields[$tableName . '/' . $field] = $field;
                }
            } else {
                throw Mage::exception('Module_Eav', 'Invalid fields');
            }
            $condition = '((' . join(') AND (', $condArr) . '))';
            $this->getSelect()->joinLeft(array("_joined_$tableName" => $table), $condition, $fields);
        }

        echo __METHOD__ . '(), line ' . __LINE__ . ': ' . $this->getSelect() . "\n<br>\n";

        if ($this->getCurPage() && $this->getPageSize()) {
            $this->getSelect()->limitPage($this->getCurPage(), $this->getPageSize());
        }

        // get data
        $rows = $this->_read->fetchAll($this->getSelect());
        if (!$rows) {
            return $this;
        }

        foreach ($rows as $v) {
            $object = clone $this->getObject();
            $this->_items[] = $object->setData($v);
//            $this->_items[$v[$this->getEntity()->getEntityIdField()]] = $object->setData($v);
        }

    }

    /**
     * Load collection data into object items
     *
     * @param integer $storeId
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function load()
    {
        if (!$this->_read) {
            throw Mage::exception('Mage_Eav', 'No connection available');
        }

        $this->_loadAll();

        return $this;
    }

    public function addFieldToFilter($attribute, $condition=null) {
        if (empty($condition)) return $this;
        print_r($condition); echo "\n<br>";
        // TODO if concatenated columns
        if (is_array($attribute)) {
            throw Mage::exception('Mage_Sales', 'Concatenated values filtering is not supported');
        } elseif (is_string($attribute)) {
            $attribute = $this->_validateAttribute($attribute);
            $this->_addJoinedAttribute($attribute, '', $condition);
            return $this;
        } else {
            throw Mage::exception('Mage_Sales', 'Unsupported filter attribute type');
        }
    }

    public function addAttributeToSort($attribute, $dir='asc')
    {
        if (is_array($attribute)) {
            list( , $attribute) = each($attribute);
        }
        $attribute = $this->_validateAttribute($attribute);
        $this->_addJoinedAttribute($attribute);
        $alias = $attribute->getAlias($this->getEntity());
        if ($attribute->getEntity()->getType() !== $this->getEntity()->getType()) {
            $alias = $this->_read->quoteIdentifier('_attribute_' . $alias) . '.' . $this->_read->quoteIdentifier('value');
        } else {
            $alias = $this->_read->quoteIdentifier('e') . '.' . $this->_read->quoteIdentifier('value');
        }
        $this->getSelect()->order($alias, $dir);
        return $this;
    }

}