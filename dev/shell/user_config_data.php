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

$usage = 'Usage: php -f user_config_data.php --
    ' . ' [--website=0|1]  - configuration is website specific, only one of \'webiste\' and \'store\' can be set'
    . ' as \'1\' at a time
    ' . ' [--store=0|1]  - configuration is store specific, only one of \'webiste\' and \'store\' can be set'
    . ' as \'1\' at a time
    ' . ' --data=<string> - pairs of \'path=value\' separated by \'&\', where
    ' . '       \'path\' is path of the specified data group, e.g. web/unsecure/base_url, and
    ' . '       \'value\' is value for the path specified, e.g. http://127.0.0.1/
    ' . ' Example Usage: php -f user_config_data.php -- --website=1
    ' . '--data=web/seo/use_rewrites=1&web/unsecure/base_url=http://127.0.0.1/' . PHP_EOL;

$longOpts = [
    'website::',
    'store::',
    'data:'
];

$opt = getopt('', $longOpts);
if (empty($opt)) {
    echo $usage;
    exit(0);
}

try {
    $data = new ComplexParameter('data');
    $params = $data->mergeFromArgv($_SERVER, $_SERVER);
    if (isset($opt['website'])) {
        $params[ScopeInterface::SCOPE_WEBSITE] = '1';
    }
    if (isset($opt['store'])) {
        $params[ScopeInterface::SCOPE_STORE] = '1';
    }
    $opt['data'] = $data->getFromString('--data=' . $opt['data']);
    $params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
    $bootstrap = Bootstrap::create(BP, $params);
    /** @var \Magento\Framework\App\Config\UserConfig $app */
    $app = $bootstrap->createApplication('Magento\Framework\App\Config\UserConfig', ['request' => $opt]);
    $bootstrap->run($app);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
