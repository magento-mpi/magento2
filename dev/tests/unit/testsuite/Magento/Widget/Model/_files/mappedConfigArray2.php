<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    '@' => array('type' => 'Magento\GiftRegistry\Block\Search\Widget\Form', 'module' => 'Magento_GiftRegistry'),
    'name' => 'Gift Registry Search',
    'description' => 'Gift Registry Quick Search Form',
    'parameters' => array(
        'types' => array(
            'type' => 'multiselect',
            'visible' => '1',
            'source_model' => 'Magento\GiftRegistry\Model\Source\Search'
        )
    )
);
