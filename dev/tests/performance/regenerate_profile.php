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
$config = require_once __DIR__ . '/framework/bootstrap.php';

$shell = new Zend_Console_Getopt(array(
    'files-s'                            => 'List of files divided by comma(,)',
    'store_views-i'                      => 'Number of store views',
    'cart_price_rules-i'                 => 'Number of shopping cart price rules.',
    'cart_price_rules_floor-i'           => 'The number of products for the first price rule.'
        . 'Increments for each next rule.',
    'cart_price_rules_first_category-i'  => 'Product category for first cart price rule to be applied.'
        . 'Increments for each next rule.',
    'categories-i'                       => 'Number of categories',
    'categories_nesting_level-i'         => 'Max nesting level for categories',
    'simple_products-i'                  => 'Simple products count',
    'configurable_products-i'            => 'Configurable products count',
    'distribute_configurable_products-i' => 'Distribute configurable products among categories (default: 0 - no)',
    'configurables_category_path-s'      => 'Category path for configurable products (default: Category 1)'
        . 'Format: "Category 1/Category .../Category N"',
    'distribute_simple_products-i'       => 'Distribute simple products among categories (default: 1 - yes)',
    'simple_category_path-s'             => 'Category path for simple products (default: Category 1)'
        . 'Format: "Category 1/Category .../Category N"',
));

\Magento\TestFramework\Helper\Cli::setOpt($shell);

$args = $shell->getOptions();
if (empty($args)) {
    echo $shell->getUsageMessage();
    exit(1);
}

$files = array_filter(explode(',', trim($shell->getOption('files'))));
if (empty($files)) {
    $files = array(
        __DIR__ . '/testsuite/fixtures/benchmark/store_views.php',
        __DIR__ . '/testsuite/fixtures/benchmark/categories.php',
        __DIR__ . '/testsuite/fixtures/benchmark/eav_variations.php',
        __DIR__ . '/testsuite/fixtures/benchmark/configurable_products.php',
        __DIR__ . '/testsuite/fixtures/benchmark/cart_price_rules.php',
        __DIR__ . '/testsuite/fixtures/benchmark/simple_products.php',
        __DIR__ . '/testsuite/fixtures/benchmark/tax_rates.php',
        __DIR__ . '/testsuite/fixtures/benchmark/customer.php',
        __DIR__ . '/testsuite/fixtures/shipping_flatrate_enabled.php',
    );
}

$logWriter = new \Zend_Log_Writer_Stream('php://output');
$logWriter->setFormatter(new \Zend_Log_Formatter_Simple('%message%' . PHP_EOL));
$logger = new \Zend_Log($logWriter);

$shell = new \Magento\Shell($logger);
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
