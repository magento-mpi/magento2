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
        'translate' => 'name description',
    ),
    'name' => 'Gift Registry Search',
    'description' => 'Gift Registry Quick Search Form',
    'parameters' => array(
        'types' => array(
            'type' => 'multiselect',
            'visible' => 'true', // verify default value is injected
            'source_model' => 'Magento_GiftRegistry_Model_Source_Search',
        ),
    ),
);