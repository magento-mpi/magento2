<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'preference_without_required_for_attribute' => array(
        '<?xml version="1.0"?><config><preference type="Some_Type_Name" /></config>',
        array("Element 'preference': The attribute 'for' is required but missing.")),
    'preference_without_required_type_attribute' => array(
        '<?xml version="1.0"?><config><preference for="Some_For_Name" /></config>',
        array("Element 'preference': The attribute 'type' is required but missing.")),
    'preferences_with_same_for_attribute_value' => array(
        '<?xml version="1.0"?>
        <config>
            <preference for="Some_For_Name" type="Some_Type_Name" />
            <preference for="Some_For_Name" type="Some_Type_Name" />
        </config>',
        array("Element 'preference': Duplicate key-sequence ['Some_For_Name'] in unique "
            . "identity-constraint 'uniquePreference'.")),
    'preferences_with_forbidden_attribute' => array(
        '<?xml version="1.0"?>
        <config><preference for="Some_For_Name" type="Some_Type_Name" forbidden="text"/></config>',
        array("Element 'preference', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'type_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><type /></config>',
        array("Element 'type': The attribute 'name' is required but missing.")),
    'type_with_same_name_attribute_value' => array(
        '<?xml version="1.0"?>
        <config>
            <type name="Some_Type_name" />
            <type name="Some_Type_name" />
        </config>',
        array("Element 'type': Duplicate key-sequence ['Some_Type_name'] in unique identity-constraint 'uniqueType'.")),
    'type_with_forbidden_attribute' => array(
        '<?xml version="1.0"?><config><type name="Some_Name" forbidden="text"/></config>',
        array("Element 'type', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'type_shared_attribute_with_invalid_value' => array(
        '<?xml version="1.0"?><config><type name="Some_Name" shared="test"/></config>',
        array("Element 'type', attribute 'shared': 'test' is not a valid value of the atomic type 'xs:boolean'.")),
    'type_param_instance_with_invalid_shared_value' => array(
        '<?xml version="1.0"?>
        <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <type name="Some_Name">
                <arguments>
                    <argument name="Param_name" xsi:type="object" shared="string">Object</argument>
                </arguments>
            </type>
        </config>',
        array("Element 'argument', attribute 'shared': The value 'string' does not match the fixed value constraint "
            . "'true'.")),
    'type_instance_with_forbidden_attribute' => array(
        '<?xml version="1.0"?>
        <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <type name="Some_Name">
                <arguments>
                    <argument name="Param_name" xsi:type="object" forbidden="text">Object</argument>
                </arguments>
            </type>
        </config>',
        array("Element 'argument', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'type_plugin_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><type name="Some_Name"><plugin /></type></config>',
        array("Element 'plugin': The attribute 'name' is required but missing.")),
    'type_plugin_with_forbidden_attribute' => array(
        '<?xml version="1.0"?>
        <config><type name="Some_Name"><plugin name="some_name" forbidden="text" /></type></config>',
        array("Element 'plugin', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'type_plugin_disabled_attribute_invalid_value' => array(
        '<?xml version="1.0"?>
        <config><type name="Some_Name"><plugin name="some_name" disabled="string" /></type></config>',
        array("Element 'plugin', attribute 'disabled': 'string' is not a valid value of the atomic "
            . "type 'xs:boolean'.")),
    'type_plugin_sortorder_attribute_invalid_value' => array(
        '<?xml version="1.0"?>
        <config><type name="Some_Name"><plugin name="some_name" sortOrder="string" /></type></config>',
        array("Element 'plugin', attribute 'sortOrder': 'string' is not a valid value of the atomic type 'xs:int'.")),
    'type_with_same_argument_name_attribute' => array(
        '<?xml version="1.0"?>
        <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <type name="Some_Name">
                <arguments>
                    <argument name="same_argument_name" xsi:type="string">value</argument>
                    <argument name="same_argument_name" xsi:type="string">value2</argument>
                </arguments>
            </type>
        </config>',
        array("Element 'argument': Duplicate key-sequence ['same_argument_name'] in key identity-constraint "
            . "'argumentName'.")),
    'virtualtype_without_required_name_attribute' => array(
        '<?xml version="1.0"?><config><virtualType /></config>',
        array("Element 'virtualType': The attribute 'name' is required but missing.")),
    'virtualtype_with_invalid_shared_attribute_value' => array(
        '<?xml version="1.0"?><config><virtualType name="virtual_name" shared="string"/></config>',
        array("Element 'virtualType', attribute 'shared': 'string' is not a valid value of the atomic "
            . "type 'xs:boolean'.")),
    'virtualtype_with_forbidden_attribute' => array(
        '<?xml version="1.0"?><config><virtualType name="virtual_name" forbidden="text"/></config>',
        array("Element 'virtualType', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'virtualtype_with_same_name_attribute_value' => array(
        '<?xml version="1.0"?><config><virtualType name="test_name" /><virtualType name="test_name" /></config>',
        array("Element 'virtualType': Duplicate key-sequence ['test_name'] in unique"
            . " identity-constraint 'uniqueVirtualType'.")),
    'virtualtype_with_same_argument_name_attribute' => array(
        '<?xml version="1.0"?>
        <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <virtualType name="virtual_name">
                <arguments>
                    <argument name="same_param_name" xsi:type="string">value</argument>
                    <argument name="same_param_name" xsi:type="string">value2</argument>
                </arguments>
            </virtualType>
        </config>',
        array(
            "Element 'argument': Duplicate key-sequence ['same_param_name'] in key identity-constraint 'argumentName'."
        )),
);
