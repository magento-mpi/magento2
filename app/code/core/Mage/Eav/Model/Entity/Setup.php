<?php

class Mage_Eav_Model_Entity_Setup extends Mage_Core_Model_Resource_Setup 
{
    public function installEntities($entities)
    {
        $conn = $this->_conn;
        
        foreach ($entities as $entityName=>&$entity) {
            $conn->delete($this->getTable('eav/entity_type'), $conn->quoteInto('entity_type_code=?', $entityName));
            
            $conn->insert($this->getTable('eav/entity_type'), array(
                'entity_type_code'=>$entityName, 
                'entity_table'=>$entity['table'], 
                'is_data_sharing'=>1,
            ));
            $entity['entity_type_id'] = $conn->lastInsertId();
            
            $conn->insert($this->getTable('eav/attribute_set'), array(
                'entity_type_id'=>$entity['entity_type_id'],
                'attribute_set_name'=>isset($entity['set_name']) ? $entity['set_name'] : 'Default',
                'sort_order'=>1,
            ));
            $entity['attribute_set_id'] = $conn->lastInsertId();
            
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
                $conn->insert($this->getTable('eav/attribute'), array(
                    'entity_type_id'=>$entity['entity_type_id'],
                    'attribute_code'=>$attrCode,
                    'backend_model'=>$backendPrefix.(isset($attr['backend']) ? $attr['backend'] : ''),
                    'backend_type'=>isset($attr['type']) ? $attr['type'] : 'varchar',
                    'frontend_model'=>$frontendPrefix.(isset($attr['frontend']) ? $attr['frontend'] : ''),
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