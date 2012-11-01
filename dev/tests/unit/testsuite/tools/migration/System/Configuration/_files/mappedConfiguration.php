<?php
/**
 * {license_notice}
 *
 * @category    Dummy
 * @package     Module_Dummy
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'comment' => '/**'
        . ' * {license_notice}'
        . ' *'
        . ' * @category  Dummy'
        . ' * @package   Module_Dummy'
        . ' * @copyright {copyright}'
        . ' * @license   {license_link}'
        . ' */',
    'nodes' => array(
        array(
            'nodeName' => 'tab',
            '@attributes' => array (
                'id' => 'tab_1',
                'sortOrder' => 10,
                'type' => 'text',
                'class' => 'css class',
            ),
            'parameters' => array (
                array(
                    'name' => 'label',
                    '#text' => 'tab label'
                ),
                array(
                    'name' => 'comment',
                    '#cdata-section' => 'tab comment'
                ),
            )
        ),
        array(
            'nodeName' => 'section',
            '@attributes' => array(
                'id' => 'section_1',
                'sortOrder' => 10,
                'type' => 'text',
            ),
            'parameters' => array(
                array(
                    'name' => 'class',
                    '#text' => 'css class'
                ),
                array(
                    'name' => 'label',
                    '#text' => 'section label'
                ),
                array(
                    'name' => 'comment',
                    '#cdata-section' => 'section comment'
                ),
                array(
                    'name' => 'resource',
                    '#text' => 'acl'
                ),
                array(
                    'name' => 'header_css',
                    '#text' => 'some css class'
                ),
                array(
                    'name' => 'tab',
                    '#text' => 'tab_1'
                ),
            ),
            'subConfig' => array(
                array(
                    'nodeName' => 'group',
                    '@attributes' => array(
                        'id' => 'group_1',
                        'sortOrder' => 10,
                        'type' => 'text',
                    ),
                    'parameters' => array(
                        array('name' => 'class', '#text' => 'css class'),
                        array('name' => 'label','#text' => 'group label'),
                        array('name' => 'comment','#cdata-section' => 'group comment'),
                        array('name' => 'resource', '#text' => 'acl'),
                        array('name' => 'fieldset_css', '#text' => 'some css class'),
                        array('name' => 'clone_fields', '#text' => 'some fields'),
                        array('name' => 'clone_model', '#text' => 'some model'),
                        array('name' => 'help_url', '#text' => 'some url'),
                        array('name' => 'hide_in_single_store_mode', '#text' => 'mode'),
                        array('name' => 'expanded', '#text' => 'yes'),
                    ),
                    'subConfig' => array(
                        array(
                            'nodeName' => 'field',
                            '@attributes' => array('id' => 'field_1'),
                            'parameters' => array(
                                array('name' => 'comment', '#cdata-section' => 'comment_test'),
                                array('name' => 'tooltip', '#text' => 'tooltip_test'),
                                array('name' => 'frontend_class', '#text' => 'frontend_class_test'),
                                array('name' => 'validate', '#text' => 'validate_test'),
                                array('name' => 'can_be_empty', '#text' => 'can_be_empty_test'),
                                array('name' => 'if_module_enabled', '#text' => 'if_module_enabled_test'),
                                array('name' => 'frontend_model', '#text' => 'frontend_model_test'),
                                array('name' => 'backend_model', '#text' => 'backend_model_test'),
                                array('name' => 'source_model', '#text' => 'source_model_test'),
                                array('name' => 'config_path', '#text' => 'config_path_test'),
                                array('name' => 'base_url', '#text' => 'base_url_test'),
                                array('name' => 'upload_dir','#text' => 'upload_dir_test'),
                                array('name' => 'button_url', '#text' => 'button_url_test',),
                                array('name' => 'button_label', '#text' => 'button_label_test'),
                                array(
                                    'name' => 'depends',
                                    'subConfig' => array(
                                        array(
                                            'nodeName' => 'field',
                                            '@attributes' => array('id' => 'module1'),
                                            '#text' => 'yes'
                                        )
                                    )
                                ),
                                array('name' => 'more_url', '#text' => 'more_url_test'),
                                array('name' => 'demo_url', '#text' => 'demo_url_test'),
                                array(
                                    '@attributes' => array(
                                        'type' => 'undefined',
                                        'some' => 'attribute',
                                    ),
                                    'name' => 'attribute',
                                    '#text' => 'undefined_test',
                                ),
                                array(
                                    '@attributes' => array(
                                        'type' => 'node'
                                    ),
                                    'name' => 'attribute',
                                    'subConfig' => array(
                                        array(
                                            'nodeName' => 'label',
                                            'subConfig' => array(
                                                array(
                                                    'nodeName' => 'nodeLabel',
                                                    '#text' => 'nodeValue',
                                                )
                                            )
                                        )
                                    )
                                )
                            ),
                        ),
                    )
                )
            )
        )
    )
);
