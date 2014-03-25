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
    'profile-s'                         => 'Profile configuration file',
));

\Magento\ToolkitFramework\Helper\Cli::setOpt($shell);

$args = $shell->getOptions();
if (empty($args)) {
    echo $shell->getUsageMessage();
    exit(1);
}

$config = \Magento\ToolkitFramework\Config::getInstance();

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
    __DIR__ . '/fixtures/disable_form_key_usage.php',
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
