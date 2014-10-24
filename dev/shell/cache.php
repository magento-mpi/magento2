<?php
/**
 * A CLI tool for managing Magento application caches
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\App;

use Magento\Framework\App\Cache\ManagerApp;
use Magento\Framework\Shell\ComplexParameter;

require __DIR__ . '/../../app/bootstrap.php';

$usage = 'Usage: php -f cache.php -- [--' . ManagerApp::KEY_SET . '=1|0]'
    . ' [--' . ManagerApp::KEY_CLEAN . ']'
    . ' [--' . ManagerApp::KEY_FLUSH . ']'
    . ' [--' . ManagerApp::KEY_TYPES . '=<type1>,<type2>,...]'
    . ' [--bootstrap=INIT_PARAM=foo&ANOTHER_PARAM[key]=bar]
    --' . ManagerApp::KEY_TYPES . ' - list of cache types, comma-separated. If omitted, all caches will be affected
    --' . ManagerApp::KEY_SET . ' - enable or disable the specified cache types
    --' . ManagerApp::KEY_CLEAN . ' - clean data of the specified cache types
    --' . ManagerApp::KEY_FLUSH . ' - destroy all data in storage that the specified cache types reside on
    --bootstrap - add or override parameters of the bootstrap' . PHP_EOL;
$longOpts = [
    ManagerApp::KEY_SET . '::',
    ManagerApp::KEY_CLEAN,
    ManagerApp::KEY_FLUSH,
    ManagerApp::KEY_TYPES . '::',
    'bootstrap::'
];
$opt = getopt('', $longOpts);
if (empty($opt)) {
    echo $usage;
}

try {
    $bootstrapParam = new ComplexParameter('bootstrap');
    $params = $bootstrapParam->mergeFromArgv($_SERVER, $_SERVER);
    $params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
    $bootstrap = Bootstrap::create(BP, $params);
    /** @var ManagerApp $app */
    $app = $bootstrap->createApplication('Magento\Framework\App\Cache\ManagerApp', ['request' => $opt]);
    $bootstrap->run($app);
    echo "Current status:\n";
    var_export($app->getStatusSummary());
    echo "\n";
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
