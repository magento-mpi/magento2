<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $config \Magento\TestFramework\Performance\Config */
$config = require_once __DIR__ . '/../../performance/framework/bootstrap.php';

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

\Magento\TestFramework\Helper\Cli::setOpt($shell);

$args = $shell->getOptions();
if (empty($args)) {
    /*echo $shell->getUsageMessage();
    exit(1);*/
}

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
);

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Shell(new \Magento\OSInfo(), $logger);
$scenarioHandler = new \Magento\TestFramework\Performance\Scenario\Handler\FileFormat();
$scenarioHandler->register('jmx', new \Magento\TestFramework\Performance\Scenario\Handler\Jmeter($shell))
    ->register('php', new \Magento\TestFramework\Performance\Scenario\Handler\Php($shell));

$testsuite = new \Magento\TestFramework\Performance\Testsuite(
    $config,
    new \Magento\TestFramework\Application($config, $shell),
    $scenarioHandler
);

$testsuite->getApplication()->reset();
$testsuite->getApplication()->bootstrap();

foreach ($files as $fixture) {
    echo 'Applying fixture ' . $fixture . PHP_EOL;
    $testsuite->getApplication()->applyFixture($fixture);
}

$testsuite->getApplication()->reindex();

$fixture = __DIR__ . '/fixtures/catalog_target_rules.php';
echo 'Applying fixture ' . $fixture . PHP_EOL;
$testsuite->getApplication()->applyFixture($fixture);
