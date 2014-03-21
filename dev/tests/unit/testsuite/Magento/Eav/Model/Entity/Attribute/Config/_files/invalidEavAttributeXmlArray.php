<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'config_only_with_entity_node' => array(
        '<?xml version="1.0"?><config><entity type="type_one" /></config>',
        array("Element 'entity': Missing child element(s). Expected is ( attribute ).")
    ),
    'field_code_must_be_unique' => array(
        '<?xml version="1.0"?><config><entity type="type_one"><attribute code="code_one"><field code="code_one_one" ' .
        'locked="true" /><field code="code_one_one" locked="true" /></attribute></entity></config>',
        array(
            "Element 'field': Duplicate key-sequence ['code_one_one'] in unique identity-constraint " .
            "'uniqueFieldCode'."
        )
    ),
    'type_attribute_is_required' => array(
        '<?xml version="1.0"?><config><entity><attribute code="code_one"><field code="code_one_one" ' .
        'locked="true" /></attribute></entity></config>',
        array("Element 'entity': The attribute 'type' is required but missing.")
    ),
    'attribute_without_required_attributes' => array(
        '<?xml version="1.0"?><config><entity type="name"><attribute><field code="code_one_one" ' .
        'locked="true" /></attribute></entity></config>',
        array("Element 'attribute': The attribute 'code' is required but missing.")
    ),
    'field_node_without_required_attributes' => array(
        '<?xml version="1.0"?><config><entity type="name"><attribute code="code"><field code="code_one_one" />' .
        '<field locked="true"/></attribute></entity></config>',
        array(
            "Element 'field': The attribute 'locked' is required but missing.",
            "Element 'field': The attribute " . "'code' is required but missing."
        )
    ),
    'locked_attribute_with_invalid_value' => array(
        '<?xml version="1.0"?><config><entity type="name"><attribute code="code"><field code="code_one" locked="7" />' .
        '<field code="code_one" locked="one_one" /></attribute></entity></config>',
        array(
            "Element 'field', attribute 'locked': '7' is not a valid value of the atomic type 'xs:boolean'.",
            "Element 'field', attribute 'locked': 'one_one' is not a valid value of the atomic type 'xs:boolean'.",
            "Element 'field': Duplicate key-sequence ['code_one'] in unique identity-constraint 'uniqueFieldCode'."
        )
    ),
    'attribute_with_type_identifierType_with_invalid_value' => array(
        '<?xml version="1.0"?><config><entity type="Name"><attribute code="code1"><field code="code_one" ' .
        'locked="true" /><field code="code::one" locked="false" /></attribute></entity></config>',
        array(
            "Element 'entity', attribute 'type': [facet 'pattern'] The value 'Name' is not accepted by the pattern " .
            "'[a-z_]+'.",
            "Element 'entity', attribute 'type': 'Name' is not a valid value of the atomic type " .
            "'identifierType'.",
            "Element 'entity', attribute 'type': Warning: No precomputed value available, the value" .
            " was either invalid or something strange happend.",
            "Element 'attribute', attribute 'code': [facet " .
            "'pattern'] The value 'code1' is not accepted by the pattern '[a-z_]+'.",
            "Element 'attribute', attribute " .
            "'code': 'code1' is not a valid value of the atomic type 'identifierType'.",
            "Element 'attribute', attribute " .
            "'code': Warning: No precomputed value available, ".
            "the value was either invalid or something strange happend.",
            "Element 'field', attribute 'code': [facet 'pattern'] ".
            "The value 'code::one' is not accepted by the pattern '" .
            "[a-z_]+'.",
            "Element 'field', attribute 'code': 'code::one' is not a valid value of the atomic type " .
            "'identifierType'.",
            "Element 'field', attribute 'code': Warning: No precomputed value available, the value " .
            "was either invalid or something strange happend."
        )
    )
);
