<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    'comment' => 'comment',
    'nodes' => array(
        array(
            'nodeName' => 'tab',
            '@attributes' => array('id' => 'tab_1', 'sortOrder' => 10, 'class' => 'css class'),
            'parameters' => array(array('name' => 'label', '#text' => 'tab label'))
        ),
        array(
            'nodeName' => 'section',
            '@attributes' => array('id' => 'section_1', 'sortOrder' => 10, 'type' => 'text'),
            'parameters' => array(
                array('name' => 'class', '#text' => 'css class'),
                array('name' => 'label', '#text' => 'section label'),
                array('name' => 'resource', '#text' => 'Magento_Adminhtml::acl'),
                array('name' => 'header_css', '#text' => 'some css class'),
                array('name' => 'tab', '#text' => 'tab_1')
            ),
            'subConfig' => array(
                array(
                    'nodeName' => 'group',
                    '@attributes' => array('id' => 'group_1', 'sortOrder' => 10, 'type' => 'text'),
                    'parameters' => array(
                        array('name' => 'label', '#text' => 'group label'),
                        array('name' => 'comment', '#cdata-section' => 'group comment'),
                        array('name' => 'fieldset_css', '#text' => 'some css class'),
                        array('name' => 'clone_fields', '#text' => '1'),
                        array('name' => 'clone_model', '#text' => 'Magento\Some\Model\Name'),
                        array('name' => 'help_url', '#text' => 'some_url'),
                        array('name' => 'hide_in_single_store_mode', '#text' => '1'),
                        array('name' => 'expanded', '#text' => '1')
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
                                array('name' => 'can_be_empty', '#text' => '1'),
                                array('name' => 'if_module_enabled', '#text' => 'Magento_Backend'),
                                array('name' => 'frontend_model', '#text' => 'Magento\Some\Model\Name'),
                                array('name' => 'backend_model', '#text' => 'Magento\Some\Model\Name'),
                                array('name' => 'source_model', '#text' => 'Magento\Some\Model\Name'),
                                array('name' => 'config_path', '#text' => 'config/path/test'),
                                array('name' => 'base_url', '#text' => 'some_url'),
                                array('name' => 'upload_dir', '#text' => 'some_directory'),
                                array('name' => 'button_url', '#text' => 'some_url'),
                                array('name' => 'button_label', '#text' => 'some_label'),
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
                                    '@attributes' => array('type' => 'undefined', 'some' => 'attribute'),
                                    'name' => 'attribute',
                                    '#text' => 'undefined_test'
                                ),
                                array(
                                    '@attributes' => array('type' => 'node'),
                                    'name' => 'attribute',
                                    'subConfig' => array(
                                        array(
                                            'nodeName' => 'label',
                                            'subConfig' => array(
                                                array('nodeName' => 'nodeLabel', '#text' => 'nodeValue')
                                            )
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )
    )
);
