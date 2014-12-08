<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return [
    'page_layout_handle_must_be_unique' => [
        '<?xml version="1.0"?><config><menuLayout name="name_one" ><pageLayout handle="node_one_two" />' .
        '<pageLayout handle="node_one_two" /></menuLayout></config>',
        [
            "Element 'pageLayout': Duplicate key-sequence ['node_one_two'] in unique identity-constraint " .
            "'uniquePageLayoutHandle'."
        ],
    ],
    'menu_layout_name_must_be_unique' => [
        '<?xml version="1.0"?><config><menuLayout name="name_one" /><menuLayout name="name_one" /></config>',
        [
            "Element 'menuLayout': Duplicate key-sequence ['name_one'] in unique identity-constraint " .
            "'uniqueMenuLayoutName'."
        ],
    ],
    'name_is_required_attribute' => [
        '<?xml version="1.0"?><config><menuLayout /></config>',
        ["Element 'menuLayout': The attribute 'name' is required but missing."],
    ],
    'handle_is_required_attribute' => [
        '<?xml version="1.0"?><config><menuLayout name="name_one"><pageLayout /></menuLayout></config>',
        ["Element 'pageLayout': The attribute 'handle' is required but missing."],
    ],
    'handle_with_required_name' => [
        '<?xml version="1.0"?><config><menuLayout name="name_one"><pageLayout handle="node12" /></menuLayout></config>',
        [
            "Element 'pageLayout', attribute 'handle': [facet 'pattern'] The value 'node12' is not accepted by the " .
            "pattern '[A-Za-z_]+'.",
            "Element 'pageLayout', attribute 'handle': 'node12' is not a valid value of the " .
            "atomic type 'handleName'.",
            "Element 'pageLayout', attribute 'handle': Warning: No precomputed value " .
            "available, the value was either invalid or something strange happend."
        ],
    ],
    'optional_attributes_with_invalid_names' => [
        '<?xml version="1.0"?><config><menuLayout name="name_one" ' .
        'label="label12" handle="handle123" isDefault="12" />' .
        '</config>',
        [
            "Element 'menuLayout', attribute 'handle': [facet 'pattern'] The value 'handle123' is not accepted by the" .
            " pattern '[A-Za-z_]+'.",
            "Element 'menuLayout', attribute 'handle': 'handle123' is not a valid value of the " .
            "atomic type 'handleName'.",
            "Element 'menuLayout', attribute 'isDefault': '12' is not a valid value of the " .
            "atomic type 'xs:boolean'."
        ],
    ]
];
