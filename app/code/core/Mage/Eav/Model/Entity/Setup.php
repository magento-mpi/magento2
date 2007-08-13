<?php

class Mage_Eav_Model_Entity_Setup extends Mage_Core_Model_Resource_Setup
{

/******************* ENTITY TYPES *****************/

	/**
	 * Add an entity type
	 * 
	 * If already exists updates the entity type with params data
	 *
	 * @param string $code
	 * @param array $params
	 * @return Mage_Eav_Model_Entity_Setup
	 */
    public function addEntityType($code, array $params)
    {
        $data = array(
            'entity_type_code'=>$code,
            'entity_table'=>isset($params['table']) ? $params['table'] : 'eav/entity',
            'increment_model'=>isset($params['increment_model']) ? $params['increment_model'] : '',
            'increment_per_store'=>isset($params['increment_per_store']) ? $params['increment_per_store'] : '',
            'is_data_sharing'=>isset($params['is_data_sharing']) ? $params['is_data_sharing'] : 1,
        );

        if ($id = $this->getEntityType($code, 'entity_type_id')) {
            $this->updateEntityType($code, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/entity_type'), $data);
            
            $entityTypeId = $this->getEntityTypeId($code);
        }
        $this->addAttributeSet($code, 'Default');
        $this->addAttributeGroup($code, 'Default', 'General');

        return $this;
    }

    /**
     * Update entity row
     *
     * @param unknown_type $code
     * @param unknown_type $field
     * @param unknown_type $value
     * @return unknown
     */
    public function updateEntityType($code, $field, $value=null)
    {
        $this->updateTableRow('eav/entity_type',
            'entity_type_id', $this->getEntityTypeId($code),
            $field, $value
        );
        return $this;
    }

    public function getEntityType($id, $field=null)
    {
        return $this->getTableRow('eav/entity_type',
            is_numeric($id) ? 'entity_type_id' : 'entity_type_code', $id,
            $field
        );
    }

    public function getEntityTypeId($entityTypeId)
    {
        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId, 'entity_type_id');
        }
        if (!is_numeric($entityTypeId)) {
            throw Mage::exception('Mage_Eav', 'wrong entity id');
        }
        return $entityTypeId;
    }

    public function removeEntityType($id)
    {
        $this->_conn->delete($this->getTable('eav/entity_type'),
            $this->_conn->quoteInto('entity_type_id=?', $this->getEntityTypeId($id))
        );
        return $this;
    }

/******************* ATTRIBUTE SETS *****************/
	
	public function getAttributeSetSortOrder($entityTypeId, $sortOrder=null)
	{
		if (!is_numeric($sortOrder)) {
			$sortOrder = $this->_conn->fetchOne("select max(sort_order)
				from ".$this->getTable('eav/attribute_set')." 
				where entity_type_id=".$this->getEntityTypeId($entityTypeId)
			);
			$sortOrder++;
		}
		return $sortOrder;
	}

    public function addAttributeSet($entityTypeId, $name, $sortOrder=null)
    {
        $data = array(
            'entity_type_id'=>$this->getEntityTypeId($entityTypeId),
            'attribute_set_name'=>$name,
            'sort_order'=>$this->getAttributeSetSortOrder($entityTypeId, $sortOrder),
        );

        if ($id = $this->getAttributeSet($entityTypeId, $name, 'attribute_set_id')) {
            $this->updateAttributeSet($entityTypeId, $id, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute_set'), $data);
            
            $this->addAttributeGroup($entityTypeId, $name, 'General');
        }

        return $this;
    }

    public function updateAttributeSet($entityTypeId, $id, $field, $value=null)
    {
        $this->updateTableRow('eav/attribute_set',
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $id),
            $field, $value,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
        return $this;
    }

    public function getAttributeSet($entityTypeId, $id, $field=null)
    {
        return $this->getTableRow('eav/attribute_set',
            is_numeric($id) ? 'attribute_set_id' : 'attribute_set_name', $id,
            $field,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
    }

    public function getAttributeSetId($entityTypeId, $setId)
    {
        if (!is_numeric($setId)) {
            $setId = $this->getAttributeSet($entityTypeId, $setId, 'attribute_set_id');
        }
        if (!is_numeric($setId)) {
            throw Mage::exception('Mage_Eav', 'wrong attribute set id');
        }
        return $setId;
    }

    public function removeAttributeSet($entityTypeId, $id)
    {
        $this->_conn->delete($this->getTable('eav/attribute_set'),
            $this->_conn->quoteInto('attribute_set_id=?', $this->getAttributeSetId($entityTypeId, $id))
        );
        return $this;
    }

/******************* ATTRIBUTE GROUPS *****************/

	public function getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder=null)
	{
		if (!is_numeric($sortOrder)) {
			$sortOrder = $this->_conn->fetchOne("select max(sort_order)
				from ".$this->getTable('eav/attribute_group')." 
				where attribute_set_id=".$this->getAttributeSetId($entityTypeId, $setId)
			);
			$sortOrder++;
		}
		return $sortOrder;
	}
	
    public function addAttributeGroup($entityTypeId, $setId, $name, $sortOrder=null)
    {
        $setId = $this->getAttributeSetId($entityTypeId, $setId);
        $data = array(
            'attribute_set_id'=>$setId,
            'attribute_group_name'=>$name,
            'sort_order'=>$this->getAttributeGroupSortOrder($entityTypeId, $setId, $sortOrder),
        );

        if ($id = $this->getAttributeGroup($entityTypeId, $setId, $name, 'attribute_group_id')) {
            $this->updateAttributeGroup($entityTypeId, $setId, $id, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute_group'), $data);
        }

        return $this;
    }

    public function updateAttributeGroup($entityTypeId, $setId, $id, $field, $value=null)
    {
        $this->updateTableRow('eav/attribute_group',
            'attribute_group_id', $this->getAttributeGroupId($entityTypeId, $setId, $id),
            $field, $value,
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $setId)
        );
        return $this;
    }

    public function getAttributeGroup($entityTypeId, $setId, $id, $field=null)
    {
        return $this->getTableRow('eav/attribute_group',
            is_numeric($id) ? 'attribute_group_id' : 'attribute_group_name', $id,
            $field,
            'attribute_set_id', $this->getAttributeSetId($entityTypeId, $setId)
        );
    }

    public function getAttributeGroupId($entityTypeId, $setId, $groupId)
    {
        if (!is_numeric($groupId)) {
            $groupId = $this->getAttributeGroup($entityTypeId, $setId, $groupId, 'attribute_group_id');
        }
        if (!is_numeric($groupId)) {
            throw Mage::exception('Mage_Eav', 'wrong attribute group id');
        }
        return $groupId;
    }
    
    public function removeAttributeGroup($entityTypeId, $setId, $id)
    {
        $this->_conn->delete($this->getTable('eav/attribute_group'),
            $this->_conn->quoteInto('attribute_group_id=?', $this->getAttributeGroupId($entityTypeId, $setId, $id))
        );
        return $this;
    }

/******************* ATTRIBUTES *****************/

	/**
	 * Add attribute to an entity type
	 * 
	 * If attribute is system will add to all existing attribute sets
	 *
	 * @param string|integer $entityTypeId
	 * @param string $code
	 * @param array $attr
	 * @return Mage_Eav_Model_Entity_Setup
	 */
    public function addAttribute($entityTypeId, $code, array $attr)
    {
        $entityTypeId = $this->getEntityTypeId($entityTypeId);
        $data = array(
            'entity_type_id'=>$entityTypeId,
            'attribute_code'=>$code,
            'backend_model'=>isset($attr['backend']) ? $attr['backend'] : '',
            'backend_type'=>isset($attr['type']) ? $attr['type'] : 'varchar',
            'backend_table'=>isset($attr['table']) ? $attr['table'] : '',
            'frontend_model'=>isset($attr['frontend']) ? $attr['frontend'] : '',
            'frontend_input'=>isset($attr['input']) ? $attr['input'] : 'text',
            'frontend_label'=>isset($attr['label']) ? $attr['label'] : '',
            'source_model'=>isset($attr['source']) ? $attr['source'] : '',
            'is_global'=>isset($attr['global']) ? $attr['global'] : 1,
            'is_visible'=>isset($attr['visible']) ? $attr['visible'] : 1,
            'is_required'=>isset($attr['required']) ? $attr['required'] : 0,
            'is_user_defined'=>isset($attr['required']) ? $attr['required'] : 0,
            'default_value'=>isset($attr['default']) ? $attr['default'] : '',
        );

        if ($id = $this->getAttribute($entityTypeId, $code, 'attribute_id')) {
            $this->updateAttribute($entityTypeId, $id, $data);
        } else {
            $this->_conn->insert($this->getTable('eav/attribute'), $data);
        }
        if (empty($attr['is_user_defined'])) {
        	$sets = $this->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
        	foreach ($sets as $set) {
        		$this->addAttributeToSet($entityTypeId, $set['attribute_set_id'], 'General', $code);
        	}
        }
        return $this;
    }
    
    public function updateAttribute($entityTypeId, $id, $field, $value=null)
    {
        $this->updateTableRow('eav/attribute',
            'attribute_id', $this->getAttributeId($entityTypeId, $id),
            $field, $value,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
        return $this;
    }

    public function getAttribute($entityTypeId, $id, $field=null)
    {
        return $this->getTableRow('eav/attribute',
            is_numeric($id) ? 'attribute_id' : 'attribute_code', $id,
            $field,
            'entity_type_id', $this->getEntityTypeId($entityTypeId)
        );
    }
    
    public function getAttributeId($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            $id = $this->getAttribute($entityTypeId, $id, 'attribute_id');
        }
        if (!is_numeric($id)) {
            throw Mage::exception('Mage_Eav', 'wrong attribute id');
        }
        return $id;
    }
    
    public function removeAttribute($entityTypeId, $code)
    {
        $this->_conn->delete($this->getTable('eav/attribute_set'),
            $this->_conn->quoteInto('attribute_id=?', $this->getAttributeId($entityTypeId, $code))
        );
        return $this;
    }
    
    public function getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder=null)
	{
		if (!is_numeric($sortOrder)) {
			$sortOrder = $this->_conn->fetchOne("select max(sort_order)
				from ".$this->getTable('eav/entity_attribute')." 
				where attribute_group_id=".$this->getAttributeGroupId($entityTypeId, $setId, $groupId)
			);
			$sortOrder++;
		}
		return $sortOrder;
	}
    
    public function addAttributeToSet($entityTypeId, $setId, $groupId, $attributeId, $sortOrder=null)
    {
    	$entityTypeId = $this->getEntityTypeId($entityTypeId);
    	$setId = $this->getAttributeSetId($entityTypeId, $setId);
    	$groupId = $this->getAttributeGroupId($entityTypeId, $setId, $groupId);
    	$attributeId = $this->getAttributeId($entityTypeId, $attributeId);
    	
    	if ($this->_conn->fetchRow("select * from ".$this->getTable('eav/entity_attribute')." where attribute_set_id=$setId and attribute_id=$attributeId")) {
    		return $this;
    	}
    	$this->_conn->insert($this->getTable('eav/entity_attribute'), array(
    		'entity_type_id'=>$entityTypeId,
    		'attribute_set_id'=>$setId,
    		'attribute_group_id'=>$groupId,
    		'attribute_id'=>$attributeId,
    		'sort_order'=>$this->getAttributeSortOrder($entityTypeId, $setId, $groupId, $sortOrder),
    	));
    }

/******************* BULK INSTALL *****************/

	public function installEntities($entities)
	{
		foreach ($entities as $entityName=>$entity) {
			$this->addEntityType($entityName, $entity);
			
            $sortOrder = 1;

            $frontendPrefix = isset($entity['frontend_prefix']) ? $entity['frontend_prefix'] : '';
            $backendPrefix = isset($entity['backend_prefix']) ? $entity['backend_prefix'] : '';
            $sourcePrefix = isset($entity['source_prefix']) ? $entity['source_prefix'] : '';

            foreach ($entity['attributes'] as $attrCode=>$attr) {
                if (isset($attr['backend'])) {
                    if ('_'===$attr['backend']) {
                        $attr['backend'] = $backendPrefix;
                    } elseif ('_'===$attr['backend']{0}) {
                        $attr['backend'] = $backendPrefix.$attr['backend'];
                    } else {
                        $attr['backend'] = $attr['backend'];
                    }
                }
                if (isset($attr['frontend'])) {
                    if ('_'===$attr['frontend']) {
                        $attr['frontend'] = $frontendPrefix;
                    } elseif ('_'===$attr['frontend']{0}) {
                        $attr['frontend'] = $frontendPrefix.$attr['frontend'];
                    } else {
                        $attr['frontend'] = $attr['frontend'];
                    }
                }
                if (isset($attr['source'])) {
                    if ('_'===$attr['source']) {
                        $attr['source'] = $sourcePrefix;
                    } elseif ('_'===$attr['source']{0}) {
                        $attr['source'] = $sourcePrefix.$attr['source'];
                    } else {
                        $attr['source'] = $attr['source'];
                    }
                }
                
                $this->addAttribute($entityName, $attrCode, $attr);
                #$this->addAttributeToSet($entityName, 'Default', 'General');
            }
		}
	}

    public function installEntities1($entities)
    {
        $conn = $this->_conn;

        foreach ($entities as $entityName=>&$entity) {
            $conn->delete($this->getTable('eav/entity_type'), $conn->quoteInto('entity_type_code=?', $entityName));

            $conn->insert($this->getTable('eav/entity_type'), array(
                'entity_type_code'=>$entityName,
                'entity_table'=>$entity['table'],
                'increment_model'=>isset($entity['increment_model']) ? $entity['increment_model'] : '',
                'increment_per_store'=>isset($entity['increment_per_store']) ? $entity['increment_per_store'] : '',
                'is_data_sharing'=>1,
            ));
            $entity['entity_type_id'] = $conn->lastInsertId();

            $conn->insert($this->getTable('eav/attribute_set'), array(
                'entity_type_id'=>$entity['entity_type_id'],
                'attribute_set_name'=>isset($entity['set_name']) ? $entity['set_name'] : 'Default',
                'sort_order'=>1,
            ));
            $entity['attribute_set_id'] = $conn->lastInsertId();

            $conn->update($this->getTable('eav/entity_type'), array(
                'default_attribute_set_id'=>$entity['attribute_set_id']
            ), $conn->quoteInto('entity_type_id=?', $entity['entity_type_id']));

            $conn->insert($this->getTable('eav/attribute_group'), array(
                'attribute_set_id'=>$entity['attribute_set_id'],
                'attribute_group_name'=>isset($entity['group_name']) ? $entity['group_name'] : 'General',
                'sort_order'=>1,
            ));
            $entity['attribute_group_id'] = $conn->lastInsertId();

            $sortOrder = 1;

            $frontendPrefix = isset($entity['frontend_prefix']) ? $entity['frontend_prefix'] : '';
            $backendPrefix = isset($entity['backend_prefix']) ? $entity['backend_prefix'] : '';
            $sourcePrefix = isset($entity['source_prefix']) ? $entity['source_prefix'] : '';

            foreach ($entity['attributes'] as $attrCode=>&$attr) {
                $backend = '';
                if (isset($attr['backend'])) {
                    if ('_'===$attr['backend']) {
                        $backend = $backendPrefix;
                    } elseif ('_'===$attr['backend']{0}) {
                        $backend = $backendPrefix.$attr['backend'];
                    } else {
                        $backend = $attr['backend'];
                    }
                }
                $frontend = '';
                if (isset($attr['frontend'])) {
                    if ('_'===$attr['frontend']) {
                        $frontend = $frontendPrefix;
                    } elseif ('_'===$attr['frontend']{0}) {
                        $frontend = $frontendPrefix.$attr['frontend'];
                    } else {
                        $frontend = $attr['frontend'];
                    }
                }
                $conn->insert($this->getTable('eav/attribute'), array(
                    'entity_type_id'=>$entity['entity_type_id'],
                    'attribute_code'=>$attrCode,
                    'backend_model'=>$backend,
                    'backend_type'=>isset($attr['type']) ? $attr['type'] : 'varchar',
                    'backend_table'=>isset($attr['table']) ? $attr['table'] : '',
                    'frontend_model'=>$frontend,
                    'frontend_input'=>isset($attr['input']) ? $attr['input'] : 'text',
                    'frontend_label'=>isset($attr['label']) ? $attr['label'] : '',
                    'source_model'=>$sourcePrefix.(isset($attr['source']) ? $attr['source'] : ''),
                    'is_global'=>isset($attr['global']) ? $attr['global'] : 1,
                    'is_visible'=>isset($attr['visible']) ? $attr['visible'] : 1,
                    'is_required'=>isset($attr['required']) ? $attr['required'] : 0,
                    'is_user_defined'=>isset($attr['required']) ? $attr['required'] : 0,
                    'default_value'=>isset($attr['default']) ? $attr['default'] : '',
                ));
                $attr['attribute_id'] = $conn->lastInsertId();

                $conn->insert($this->getTable('eav/entity_attribute'), array(
                    'entity_type_id'=>$entity['entity_type_id'],
                    'attribute_set_id'=>$entity['attribute_set_id'],
                    'attribute_group_id'=>$entity['attribute_group_id'],
                    'attribute_id'=>$attr['attribute_id'],
                    'sort_order'=>$sortOrder++,
                ));
                $attr['entity_attribute_id'] = $conn->lastInsertId();
            }
        }
        return $this;
    }
}