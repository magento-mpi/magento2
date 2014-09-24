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

require __DIR__ . '/../../app/bootstrap.php';

define(
    'USAGE',
    'Usage: php -f cache.php -- [--' . ManagerApp::KEY_SET . '=1|0]'
        . ' [--' . ManagerApp::KEY_CLEAN . ']'
        . ' [--' . ManagerApp::KEY_FLUSH . ']'
        . ' [--' . ManagerApp::KEY_TYPES . '=<type1>,<type2>,...]'
        . ' [--bootstrap=<json>]
        --' . ManagerApp::KEY_TYPES . ' - list of cache types, comma-separated. If omitted, all caches will be affected
        --' . ManagerApp::KEY_SET . ' - enable or disable the specified cache types
        --' . ManagerApp::KEY_CLEAN . ' - clean data of the specified cache types
        --' . ManagerApp::KEY_FLUSH . ' - destroy all data on storage that specified cache types reside on
        --bootstrap - add or override parameters of the bootstrap' . PHP_EOL
);
$opt = getopt(
    '',
    [
        ManagerApp::KEY_SET . '::',
        ManagerApp::KEY_CLEAN,
        ManagerApp::KEY_FLUSH,
        ManagerApp::KEY_TYPES . '::',
        'bootstrap::'
    ]
);
if (empty($opt)) {
    echo USAGE;
}

try {
    $params = $_SERVER;
    if (isset($opt['bootstrap'])) {
        $extra = json_decode($opt['bootstrap'], true);
        if (!is_array($extra)) {
            throw new \Exception("Unable to decode JSON in the parameter 'bootstrap'");
        }
        $params = array_replace_recursive($params, $extra);
    }
    $bootstrap = Bootstrap::create(BP, $params);
    /** @var ManagerApp $app */
    $app = $bootstrap->createApplication('Magento\Framework\App\Cache\ManagerApp', ['request' => $opt]);
    $response = $app->launch();
    echo "Current status:\n";
    var_export($app->getStatusSummary());
    $response->sendResponse();
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
