<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     toolkit_framework
 * @copyright   {copyright}
 * @license     {license_link}
 */

$applicationBaseDir = require_once __DIR__ . '/framework/bootstrap.php';

$shell = new Zend_Console_Getopt(array(
    'websites-i'                         => 'Number of websites',
    'store_groups-i'                     => 'Number of store groups',
    'store_views-i'                      => 'Number of store views',
    'categories-i'                       => 'Number of categories',
    'categories_nesting_level-i'         => 'Max nesting level for categories',
    'simple_products-i'                  => 'Number of simple products',
    'configurable_products-i'            => 'Number of configurable products',
    'customers-i'                        => 'Number of customers',

    'cart_price_rules-i'                 => 'Number of shopping cart price rules.',
    'cart_price_rules_floor-i'           => 'The number of products for the first price rule.'
        . 'Increments for each next rule.',
    'catalog_price_rules-i'              => 'Number of catalog price rules.',
    'catalog_target_rules-i'             => 'Number of catalog target rules.',
));

\Magento\ToolkitFramework\Helper\Cli::setOpt($shell);
$args = $shell->getOptions();

$files = array(
    __DIR__ . '/fixtures/stores.php',
    __DIR__ . '/fixtures/categories.php',
    __DIR__ . '/fixtures/simple_products.php',
    __DIR__ . '/fixtures/eav_variations.php',
    __DIR__ . '/fixtures/configurable_products.php',
    __DIR__ . '/fixtures/customers.php',
    __DIR__ . '/fixtures/cart_price_rules.php',
    __DIR__ . '/fixtures/catalog_price_rules.php',
    __DIR__ . '/fixtures/tax_rates.php',
    __DIR__ . '/fixtures/shipping_flatrate_enabled.php',
    __DIR__ . '/fixtures/catalog_target_rules.php'
);

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Shell(new \Magento\OSInfo(), $logger);

$application = new \Magento\ToolkitFramework\Application($applicationBaseDir, $shell);
$application->bootstrap();

foreach ($files as $fixture) {
    echo 'Applying fixture ' . $fixture . PHP_EOL;
    $application->applyFixture($fixture);
}

$application->reindex();
