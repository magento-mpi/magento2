<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
return [
    'callback_class_must_be_unique' => [
        '<?xml version="1.0"?><config><group name="collection_load_before" processor="c"><callback class="class_one" ' .
        'method="methodOne"/><callback class="class_one" method="methodTwo"/></group></config>',
        [
            "Element 'callback': Duplicate key-sequence ['class_one'] in unique identity-constraint " .
            "'uniqueCallbackClass'."
        ],
    ],
    'missed_group_processor' => [
        '<?xml version="1.0"?><config><group name="collection_load_before"><callback class="class_one" ' .
        'method="methodOne"/></group></config>',
        [
            "Element 'group': The attribute 'processor' is required but missing."
        ],
    ],
    'level_name_must_be_unique' => [
        '<?xml version="1.0"?><config><aclDeny><level name="name_one"><rule name="name_one" resource="One::Two"/>' .
        '</level><level name="name_one"><rule name="name_two" resource="One::Three"/></level></aclDeny></config>',
        [
            "Element 'level': Duplicate key-sequence ['name_one'] in unique identity-constraint " .
            "'uniqueLevelName'."
        ],
    ],
    'level_without_rules' => [
        '<?xml version="1.0"?><config><aclDeny><level name="name_two"/>' . '</aclDeny></config>',
        ["Element 'level': Missing child element(s). Expected is ( rule )."],
    ],
    'rule_name_must_be_unique' => [
        '<?xml version="1.0"?><config><aclDeny><level name="name_one"><rule name="name_one" resource="One::Two"/>' .
        '</level><level name="name_two"><rule name="name_one" resource="One::Three"/><rule name="name_one" ' .
        'resource="One::Three"/></level></aclDeny></config>',
        [
            "Element 'rule': Duplicate key-sequence ['name_one'] in unique identity-constraint " . "'uniqueRuleName'."
        ],
    ],
    'group_name_must_be_unique' => [
        '<?xml version="1.0"?><config><group name="collection_load_before" processor="c"><callback class="class_one" ' .
        'method="methodOne"/><callback class="class_two" method="methodTwo"/></group><group name= ' .
        '"collection_load_before" processor="c"><callback class="class_two" method="methodOne"/></group></config>',
        [
            "Element 'group': Duplicate key-sequence ['collection_load_before'] in unique identity-constraint " .
            "'uniqueGroupName'."
        ],
    ],
    'group_name_is_required' => [
        '<?xml version="1.0"?><config><group processor="c"><callback class="class_one" method="methodOne"/></group>' .
        '</config>',
        ["Element 'group': The attribute 'name' is required but missing."],
    ],
    'callback_method_and_class_are_required' => [
        '<?xml version="1.0"?><config><group name="name" processor="c"><callback class="class_one"/>' .
        '<callback method="method"/></group></config>',
        [
            "Element 'callback': The attribute 'method' is required but missing.",
            "Element 'callback': The attribute" . " 'class' is required but missing."
        ],
    ],
    'level_has_required_attribute_name' => [
        '<?xml version="1.0"?><config><aclDeny><level><rule name="name_one" resource="One::Two"/>' .
        '</level><level name="name_two"><rule name="name_two" resource="One::Three"/><rule name="name_one" ' .
        'resource="One::Three"/></level></aclDeny></config>',
        ["Element 'level': The attribute 'name' is required but missing."],
    ],
    'rule_has_required_attributes_name_and_resource' => [
        '<?xml version="1.0"?><config><aclDeny><level name="name_one"><rule />' .
        '</level><level name="name_two"><rule name="name_two" resource="One::Three"/><rule name="name_one" ' .
        'resource="One::Three"/></level></aclDeny></config>',
        [
            "Element 'rule': The attribute 'name' is required but missing.",
            "Element 'rule': The attribute " . "'resource' is required but missing."
        ],
    ],
    'resource_with_invalid_value' => [
        '<?xml version="1.0"?><config><aclDeny><level name="name_one"><rule name="name_two" resource="One::Three"/>' .
        '</level><level name="name_two"><rule name="name_two" resource="One/Three"/><rule name="name_one" ' .
        'resource="One:Three"/></level></aclDeny></config>',
        [
            "Element 'rule', attribute 'resource': [facet 'pattern'] The value 'One/Three' is not accepted by the " .
            "pattern '[a-zA-Z_:]+'.",
            "Element 'rule', attribute 'resource': 'One/Three' is not a valid value of the " .
            "atomic type 'resourceName'."
        ],
    ],
    'method_with_invalid_value' => [
        '<?xml version="1.0"?><config><group name="collection_load_before" processor="c"><callback class="class_one" ' .
        'method="method12"/><callback class="class_two" method="methodTwo"/></group></config>',
        [
            "Element 'callback', attribute 'method': [facet 'pattern'] The value 'method12' is not accepted by the " .
            "pattern '[a-zA-Z]+'.",
            "Element 'callback', attribute 'method': 'method12' is not a valid value of the " .
            "atomic type 'methodName'."
        ],
    ]
];
