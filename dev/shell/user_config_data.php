<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\Shell\ComplexParameter;
use Magento\Store\Model\ScopeInterface;
use Magento\Backend\Model\Config;
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../../app/bootstrap.php';

$usage = 'Usage: php -f user_config_data.php -- '
    . '--data=<string> - pairs of \'path=value\' separated by \'&\', where '. PHP_EOL
    . '       \'path\' is path of the specified data group, e.g. web/unsecure/base_url, and ' . PHP_EOL
    . '       \'value\' is value for the path specified, e.g. http://127.0.0.1/ ' . PHP_EOL
    . '       --bootstrap - add or override parameters of the bootstrap' . PHP_EOL
    . ' NOTE: this tool supports writing data only in global scope ' . PHP_EOL
    . ' Example Usage: php -f user_config_data.php -- '
    . ' --data=' . escapeshellarg('web/seo/use_rewrites=1&web/unsecure/base_url=http://127.0.0.1/') . PHP_EOL;


$opt = getopt('', ['data:']);
if (empty($opt)) {
    echo $usage;
    exit(0);
}

try {
    $dataParam = new ComplexParameter('data');
    $request = $dataParam->mergeFromArgv($_SERVER);
    $bootstrapParam = new ComplexParameter('bootstrap');
    $params = $bootstrapParam->mergeFromArgv($_SERVER, $_SERVER);
    $params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
    $bootstrap = Bootstrap::create(BP, $params);
    /** @var \Magento\Backend\App\UserConfig $app */
    $app = $bootstrap->createApplication('Magento\Backend\App\UserConfig', ['request' => $request]);
    $bootstrap->run($app);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
