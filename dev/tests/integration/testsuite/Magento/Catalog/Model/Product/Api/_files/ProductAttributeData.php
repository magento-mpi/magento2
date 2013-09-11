<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */


return array(
    'create_text_api' => array(
        'attribute_code' => 'a_text_api',
        'scope' => 'store',
        'frontend_input' => 'text',
        'default_value' => '',
        'is_unique' => '0',
        'is_required' => '0',
        'apply_to' => array(
            'simple',
            'grouped',
        ),
        'is_configurable' => '0',
        'is_searchable' => '1',
        'is_visible_in_advanced_search' => '0',
        'is_comparable' => '1',
        'is_used_for_promo_rules' => '0',
        'is_visible_on_front' => '1',
        'used_in_product_listing' => '0',
        //'label' => 'a_text_api',
        'frontend_label' => array(
            array(
                'store_id' => 0,
                'label' => 'a_text_api'
            ),
            array(
                'store_id' => 1,
                'label' => 'a_text_api'
            ),
        ),
    ),
    'create_select_api' => array(
        'attribute_code' => 'a_select_api',
        'scope' => 'store',
        'frontend_input' => 'select',
        'default_value' => '',
        'is_unique' => '0',
        'is_required' => '0',
        'apply_to' => array(
            'simple',
            'grouped',
        ),
        'is_configurable' => '0',
        'is_searchable' => '1',
        'is_visible_in_advanced_search' => '0',
        'is_comparable' => '1',
        'is_used_for_promo_rules' => '0',
        'is_visible_on_front' => '1',
        'used_in_product_listing' => '0',
        //'label' => 'a_select_api',
        'frontend_label' => array(
            array(
                'store_id' => 0,
                'label' => 'a_select_api'
            ),
            array(
                'store_id' => 1,
                'label' => 'a_select_api'
            ),
        ),
    ),
    'create_text_installer' => array(
        'code' => 'a_text_ins',
        'attributeData' => array(
            'type' => 'varchar',
            'input' => 'text',
            'label' => 'a_text_ins',
            'required' => 0,
            'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
            'user_defined' => true,

        ),
    ),
    'create_select_installer' => array(
        'code' => 'a_select_ins',
        'attributeData' => array(
            'type' => 'int',
            'input' => 'select',
            'label' => 'a_select_ins',
            'required' => 0,
            'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
            'user_defined' => true,
            'option' => array(
                'values' => array(
                    'option1',
                    'option2',
                    'option3',
                ),
            )
        ),
    ),
    'create_select_api_options' => array(
        array(
            'label' => array(
                array(
                    'store_id' => 0,
                    'value' => 'option1'
                ),
                array(
                    'store_id' => 1,
                    'value' => 'option1'
                ),
            ),
            'order' => 0,
            'is_default' => 1
        ),
        array(
            'label' => array(
                array(
                    'store_id' => 0,
                    'value' => 'option2'
                ),
                array(
                    'store_id' => 1,
                    'value' => 'option2'
                ),
            ),
            'order' => 0,
            'is_default' => 1
        ),
    ),
);

