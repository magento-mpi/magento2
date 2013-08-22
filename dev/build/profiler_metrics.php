<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
?>
<?php return array(
    'test execution time (ms)' =>            array('integration_test'),
    /* Application framework metrics */
    'bootstrap time (ms)' =>                 array('bootstrap'),
    'modules initialization time (ms)' =>    array('init_modules'),
    'request initialization time (ms)' =>    array('init_request'),
    'routing time (ms)' =>                   array(
        'routing_init', 'db_url_rewrite', 'config_url_rewrite', 'routing_match_router'
    ),
    'pre dispatching time (ms)' =>           array('predispatch'),
    'layout overhead time (ms)' =>           array('layout_load', 'layout_generate_xml', 'layout_generate_blocks'),
    'response rendering time (ms)' =>        array('layout_render'),
    'post dispatching time (ms)' =>          array('postdispatch', 'response_send'),
    /* Magento_Catalog module metrics */
    'product save time (ms)' =>              array('catalog_product_save'),
    'product load time (ms)' =>              array('catalog_product_load'),
    'category save time (ms)' =>             array('catalog_category_save'),
    'category load time (ms)' =>             array('catalog_category_load'),
);
