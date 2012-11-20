<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
 

return array(
    'config' => array(
        'system' => array(
            'tabs' => array(
                'tab_1' => array(
                    'id' => 'tab_1',
                    'label' => 'Tab 1 New'
                )
            ),
            'sections' => array(
                'section_1' => array(
                    '_elementType' => 'section',
                    'id' => 'section_1',
                    'type' => 'text',
                    'label' => 'Section 1 New',
                    'tab' => 'tab_1',
                    'children' => array(
                        'group_1' => array(
                            '_elementType' => 'group',
                            'id' => 'group_1',
                            'type' => 'text',
                            'label' => 'Group 1 New',
                            'children' => array(
                                'field_2' => array(
                                    '_elementType' => 'field',
                                    'id' => 'field_2',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'type' => 'text',
                                    'label' => 'Field 2',
                                    'backend_model' => 'Mage_Backend_Model_Config_Backend_Encrypted'
                                )
                            ),
                        ),
                        'group_2' => array(
                            '_elementType' => 'group',
                            'id' => 'group_2',
                            'type' => 'text',
                            'label' => 'Group 2',
                            'children' => array(
                                'field_3' => array(
                                    '_elementType' => 'field',
                                    'id' => 'field_3',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'type' => 'text',
                                    'label' => 'Field 3',
                                )
                            ),
                        )
                    ),
                ),
                'section_2' => array(
                    '_elementType' => 'section',
                    'id' => 'section_2',
                    'type' => 'text',
                    'label' => 'Section 2',
                    'tab' => 'tab_2',
                    'children' => array(
                        'group_3' => array(
                            '_elementType' => 'group',
                            'id' => 'group_3',
                            'type' => 'text',
                            'label' => 'Group 3',
                            'comment' => '<a href="test_url">test_link</a>',
                            'children' => array(
                                'field_3' => array(
                                    '_elementType' => 'field',
                                    'id' => 'field_3',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'type' => 'text',
                                    'label' => 'Field 3',
                                    'attribute_0' => array(
                                        'someArr' => array(
                                            'someVal' => 1
                                        )
                                    ),
                                    'depends' => array(
                                        'fields' => array(
                                            'field_4' => array(
                                                'id' => 'field_4',
                                                'value' => 'someValue'
                                            ),
                                            'field_1' => array(
                                                'id' => 'field_1',
                                                'value' => 'someValue'
                                            )
                                        )
                                    )
                                ),
                                'field_4' => array(
                                    '_elementType' => 'field',
                                    'id' => 'field_4',
                                    'translate' => 'label',
                                    'showInWebsite' => '1',
                                    'type' => 'text',
                                    'label' => 'Field 4',
                                    'backend_model' => 'Mage_Backend_Model_Config_Backend_Encrypted',
                                    'attribute_1' => 'test_value_1',
                                    'attribute_2' => 'test_value_2',
                                    'attribute_text' => '<test_value>',
                                    'attribute_text_in_array' => array(
                                        'var' => '<a href="test_url">test_link</a>',
                                        'type' => 'someType'
                                    ),
                                    'depends' => array(
                                        'fields' => array(
                                            'field_3' => array(
                                                'id' => 'field_3',
                                                'value' => 0
                                            )
                                        )
                                    )
                                )
                            ),
                        )
                    )
                )
            )
        )
    )
);
