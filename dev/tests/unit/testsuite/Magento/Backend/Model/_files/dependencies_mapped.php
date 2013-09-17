<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'config' => array(
        'system' => array(
            'sections' => array(
                'section_1' => array(
                    'id' => 'section_1',
                    '_elementType' => 'section',
                    'children' => array(
                        'group_1' => array(
                            'id' => 'group_1',
                            '_elementType' => 'group',
                            'path' => 'section_1',
                            'depends' => array(
                                'fields' => array(
                                    'field_2' => array(
                                        'id' => 'section_1/group_1/field_2',
                                        'value' => 10,
                                        'dependPath' => array(
                                            'section_1',
                                            'group_1',
                                            'field_2',
                                        ),
                                    ),
                                ),
                            ),
                            'children' => array(
                                'field_2' => array(
                                    'id' => 'field_2',
                                    '_elementType' => 'field',
                                ),
                            ),
                        ),
                        'group_2' => array(
                            'id' => 'group_2',
                            '_elementType' => 'group',
                            'children' => array(
                                'field_3' => array(
                                    'id' => 'field_3',
                                    '_elementType' => 'field',
                                ),
                            ),
                        ),
                    ),
                ),
                'section_2' => array(
                    'id' => 'section_2',
                    '_elementType' => 'section',
                    'children' => array(
                        'group_3' => array(
                            'id' => 'group_3',
                            '_elementType' => 'group',
                            'children' => array(
                                'field_3' => array(
                                    'id' => 'field_3',
                                    '_elementType' => 'field',
                                    'path' => 'section_2/group_3',
                                    'depends' => array(
                                        'fields' => array(
                                            'field_4' => array(
                                                'id' => 'section_2/group_3/field_4',
                                                'value' => 'someValue',
                                                'dependPath' => array(
                                                    'section_2',
                                                    'group_3',
                                                    'field_4',
                                                ),
                                            ),
                                            'field_1' => array(
                                                'id' => 'section_1/group_3/field_1',
                                                'value' => 'someValue',
                                                'dependPath' => array(
                                                    'section_1',
                                                    'group_3',
                                                    'field_1',
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                                'field_4' => array(
                                    'id' => 'field_4',
                                    '_elementType' => 'field',
                                    'path' => 'section_2/group_3',
                                    'depends' => array(
                                        'fields' => array(
                                            'field_3' => array(
                                                'id' => 'section_2/group_3/field_3',
                                                'value' => 0,
                                                'dependPath' => array(
                                                    'section_2',
                                                    'group_3',
                                                    'field_3',
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                                'group_4_1' => array(
                                    'id' => 'group_4_1',
                                    '_elementType' => 'group',
                                    'path' => 'section_2/group_3',
                                    'depends' => array(
                                        'fields' => array(
                                            'field_3' => array(
                                                'id' => 'section_2/group_3/field_3',
                                                'value' => 0,
                                                'dependPath' => array(
                                                    'section_2',
                                                    'group_3',
                                                    'field_3',
                                                ),
                                            ),
                                        ),
                                    ),
                                    'children' => array(
                                        'field_5' => array(
                                            'id' => 'field_5',
                                            '_elementType' => 'field',
                                            'path' => 'section_2/group_3/group_4_1',
                                            'depends' => array(
                                                'fields' => array(
                                                    'field_4' => array(
                                                        'id' => 'section_2/group_3/group_4_1/field_4',
                                                        'value' => 'someValue',
                                                        'dependPath' => array(
                                                            'section_2',
                                                            'group_3',
                                                            'group_4_1',
                                                            'field_4',
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);
