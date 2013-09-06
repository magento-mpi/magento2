<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '@' => array(
        'type' => 'Magento_GiftRegistry_Block_Search_Widget_Form',
        'module' => 'Magento_GiftRegistry',
    ),
    'name' => 'Gift Registry Search',
    'description' => 'Gift Registry Quick Search Form',
    'parameters' => array(
        'types' => array(
            'type' => 'multiselect',
            'visible' => '1',
            'source_model' => 'Magento_GiftRegistry_Model_Source_Search',
        ),
    ),
);