<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'without_required_resource_handle' => array(
        '<?xml version="1.0"?><config></config>',
        array("Element 'config': Missing child element(s). Expected is ( resource ).")
    ),
    'resource_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><resource /></config>',
        array("Element 'resource': The attribute 'name' is required but missing.")
    ),
    'resource_name_attribute_invalid_value' => array(
        '<?xml version="1.0"?><config><resource name="testinvalidname1" /></config>',
        array(
            "Element 'resource', attribute 'name': [facet 'pattern'] The value 'testinvalidname1' is not accepted" .
            " by the pattern '[A-Za-z_]+'.",
            "Element 'resource', attribute 'name': 'testinvalidname1' is not a valid value of the atomic " .
            "type 'nameIdentifier'.",
            "Element 'resource', attribute 'name': Warning: No precomputed value available, the value was either " .
            "invalid or something strange happend."
        )
    ),
    'resource_extends_attribute_invalid_value' => array(
        '<?xml version="1.0"?><config><resource name="test_name" extends="test1"/></config>',
        array(
            "Element 'resource', attribute 'extends': [facet 'pattern'] The value 'test1' is not accepted " .
            "by the pattern '[A-Za-z_]+'.",
            "Element 'resource', attribute 'extends': 'test1' is not a valid value of the atomic type 'nameIdentifier'."
        )
    ),
    'resource_connection_attribute_invalid_value' => array(
        '<?xml version="1.0"?><config><resource name="test_name" connection="test1"/></config>',
        array(
            "Element 'resource', attribute 'connection': [facet 'pattern'] The value 'test1' is not accepted " .
            "by the pattern '[A-Za-z_]+'.",
            "Element 'resource', attribute 'connection': 'test1' is not a valid value of the atomic" .
            " type 'nameIdentifier'."
        )
    ),
    'resource_with_notallowed_attribute' => array(
        '<?xml version="1.0"?><config><resource name="test_name" notallowed="test" /></config>',
        array("Element 'resource', attribute 'notallowed': The attribute 'notallowed' is not allowed.")
    ),
    'resource_with_same_name_value' => array(
        '<?xml version="1.0"?><config><resource name="test_name" /><resource name="test_name" /></config>',
        array(
            "Element 'resource': Duplicate key-sequence ['test_name'] in unique " .
            "identity-constraint 'uniqueResourceName'."
        )
    )
);
