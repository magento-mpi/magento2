<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array
(
    'entity_same_name_attribute_value' => array(
        '<?xml version="1.0"?><config><entity name="same_name"/><entity name="same_name"/></config>',
        array("Element 'entity': Duplicate key-sequence ['same_name'] in unique "
            . "identity-constraint 'uniqueEntityName'.")),
    'entity_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><entity /></config>',
        array("Element 'entity': The attribute 'name' is required but missing.")),
    'entity_with_invalid_model_value' => array(
        '<?xml version="1.0"?><config><entity name="some_name" model="12345"/></config>',
        array("Element 'entity', attribute 'model': [facet 'pattern'] The value '12345' is not accepted by "
            . "the pattern '[A-Za-z_]+'.",
        "Element 'entity', attribute 'model': '12345' is not a valid value of the atomic type 'modelName'.")),
    'entity_with_invalid_behaviormodel_value' => array(
        '<?xml version="1.0"?><config><entity name="some_name" behaviorModel="=--09"/></config>',
        array("Element 'entity', attribute 'behaviorModel': [facet 'pattern'] The value '=--09' is not "
            . "accepted by the pattern '[A-Za-z_]+'.",
        "Element 'entity', attribute 'behaviorModel': '=--09' is not a valid value of the atomic type 'modelName'.")),
    'entity_with_notallowed_attribute' => array(
        '<?xml version="1.0"?><config><entity name="some_name" notallowd="aasd"/></config>',
        array("Element 'entity', attribute 'notallowd': The attribute 'notallowd' is not allowed.")),
    'producttype_with_same_name_attribute_value' => array(
        '<?xml version="1.0"?><config><productType name="same_name" model="model_name" />'
            . '<productType name="same_name" model="model_name" /></config>',
        array("Element 'productType': Duplicate key-sequence ['same_name'] in unique "
        . "identity-constraint 'uniqueProductTypeName'.")),
    'producttype_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><productType model="model_name" /></config>',
        array("Element 'productType': The attribute 'name' is required but missing.")),
    'producttype_without_required_model_attribute' => array(
        '<?xml version="1.0"?><config><productType name="some_name" /></config>',
        array("Element 'productType': The attribute 'model' is required but missing.")),
    'producttype_with_invalid_model_attribute_value' => array(
        '<?xml version="1.0"?><config><productType name="some_name" model="test1"/></config>',
        array("Element 'productType', attribute 'model': [facet 'pattern'] The value 'test1' is not "
            . "accepted by the pattern '[A-Za-z_]+'.",
        "Element 'productType', attribute 'model': 'test1' is not a valid value of the atomic type 'modelName'.")),
    'producttype_with_notallowed' => array(
        '<?xml version="1.0"?><config><productType name="some_name" model="test" notallowed="test"/></config>',
        array("Element 'productType', attribute 'notallowed': The attribute 'notallowed' is not allowed.")),
);