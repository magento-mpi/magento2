<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'export_entity_name_must_be_unique' => array(
        '<?xml version="1.0"?><config><entity name="name_one" /><entity name="name_one" /></config>',
        array("Element 'entity': Duplicate key-sequence ['name_one'] in unique identity-constraint "
        . "'uniqueEntityName'.")),
    'export_productType_name_must_be_unique' => array(
        '<?xml version="1.0"?><config><productType name="name_one" model="model"/><productType name="name_one" '
        . 'model="model" /></config>',
        array("Element 'productType': Duplicate key-sequence ['name_one'] in unique identity-constraint "
        . "'uniqueProductTypeName'.")),
    'export_fileFormat_name_must_be_unique' => array(
        '<?xml version="1.0"?><config><fileFormat name="name_one" /><fileFormat name="name_one" /></config>',
        array("Element 'fileFormat': Duplicate key-sequence ['name_one'] in unique identity-constraint "
        . "'uniqueFileFormatName'.")),
    'attributes_with_type_modelName_and_invalid_value' => array(
        '<?xml version="1.0"?><config><entity name="Name/one" model="model_one"/><productType name="name_one" '
        . ' model="1"/><fileFormat name="name_one" model="model1"/></config>',
        array("Element 'productType', attribute 'model': [facet 'pattern'] The value '1' is not accepted by the "
        . "pattern '[A-Za-z_]+'.", "Element 'productType', attribute 'model': '1' is not a valid value of the atomic "
        . "type 'modelName'.", "Element 'fileFormat', attribute 'model': [facet 'pattern'] The value 'model1' is not "
        . "accepted by the pattern '[A-Za-z_]+'.", "Element 'fileFormat', attribute 'model': 'model1' is not a valid "
        . "value of the atomic type 'modelName'.")),
    'productType_node_with_required_attribute' => array(
        '<?xml version="1.0"?><config><productType name="name_one" /><productType model="model" /></config>',
        array("Element 'productType': The attribute 'model' is required but missing.", "Element 'productType': "
        . "The attribute 'name' is required but missing.")),
    'fileFormat_node_with_required_attribute' => array(
        '<?xml version="1.0"?><config><fileFormat label="name_one" /></config>',
        array("Element 'fileFormat': The attribute 'name' is required but missing.")),
    'entity_node_with_required_attribute' => array(
        '<?xml version="1.0"?><config><entity label="name_one" /></config>',
        array("Element 'entity': The attribute 'name' is required but missing.")),
);
