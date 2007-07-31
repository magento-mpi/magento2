<?php

$entities = array(
    'quote'=>array(
        'attributes' => array(
        ),
    ), 
    'quote_address' => array(
        'attributes' => array(
        ),
    ), 
    'quote_item' => array(
        'attributes' => array(
        ),
    ), 
    'quote_payment' => array(
        'attributes' => array(
        ),
    ),
);

$tables = array(
    'entity_type' => Mage::registry('resource')->getTableName('eav/entity_type'),
    'attribute_set' => Mage::registry('resource')->getTableName('eav/entity_attribute_set'),
    'attribute_group' => Mage::registry('resource')->getTableName('eav/entity_attribute_group'),
    'attribute' => Mage::registry('resource')->getTableName('eav/attribute'),
    'entity_attribute' => Mage::registry('resource')->getTableName('eav/entity_attribute'),
);

foreach ($entities as $entityName=>&$entity) {
    $conn->insert($tables['entity_table'], array(
        'entity_name'=>$entityName, 
        'entity_table'=>'sales/quote', 
        'is_data_sharing'=>1,
    ));
    $entity['entity_type_id'] = $conn->lastInsertId();
    
    $conn->insert($tables['attribute_set'], array(
        'entity_type_id'=>$entity['entity_type_id'],
        'attribute_set_name'=>'Default',
        'sort_order'=>1,
    ));
    $entity['attribute_set_id'] = $conn->lastInsertId();
    
    $conn->insert($tables['attribute_group'], array(
        'attribute_set_id'=>$entity['attribute_set_id'],
        'attribute_group_name'=>'General',
        'sort_order'=>1,
    ));
    $entity['attribute_group_id'] = $conn->lastInsertId();
    
    $i = 0;
    foreach ($entity['attributes'] as $attrName=>&$attr) {
        $conn->insert($tables['attribute'], array(
            'entity_type_id'=>$entity['entity_type_id'],
            'attribute_code'=>$attrName,
            'backend_model'=>isset($attr['backend']) ? $attr['backend'] : '',
            'backend_type'=>isset($attr['type']) ? $attr['type'] : 'varchar',
            'frontend'=>isset($attr['frontend']) ? $attr['frontend'] : '',
            'frontend_input'=>isset($attr['input']) ? $attr['input'] : 'text',
            'frontend_label'=>isset($attr['label']) ? $attr['label'] : '',
            'source_model'=>isset($attr['source']) ? $attr['source'] : '',
            'is_global'=>1,
            'is_visible'=>isset($attr['visible']) ? $attr['visible'] : 1,
            'is_required'=>isset($attr['required']) ? $attr['required'] : 0,
            'is_user_defined'=>isset($attr['required']) ? $attr['required'] : 0,
            'default_value'=>isset($attr['default']) ? $attr['default'] : '',
        ));
        $attr['attribute_id'] = $conn->lastInsertId();
        
        $conn->insert($tables['entity_attribute'], array(
            'entity_type_id'=>$entity['entity_type_id'],
            'attribute_set_id'=>$entity['attribute_set_id'],
            'attribute_group_id'=>$entity['attribute_group_id'],
            'attribute_id'=>$attr['attribute_id'],
            'sort_order'=>++$i,
        ));
        $attr['entity_attribute_id'] = $conn->lastInsertId();
    }
}

