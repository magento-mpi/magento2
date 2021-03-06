<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

use \Magento\Framework\App\Bootstrap;
use \Magento\Framework\App\State;
use \Magento\Framework\Shell\ComplexParameter;

require_once __DIR__ . '/../../../../../app/bootstrap.php';

$usage = 'Usage: php -f install.php -- --admin_username= [--bootstrap=]
    --admin_username - store\'s admin username. Required for installation.
    [--bootstrap] - add or override parameters of the bootstrap' . PHP_EOL;

$data = getopt('', ['admin_username:', 'bootstrap::']);
if (!isset($data['admin_username']) || empty($data['admin_username'])) {
    echo $usage;
    exit(1);
}

$bootstrapParam = new ComplexParameter('bootstrap');
$params = $bootstrapParam->mergeFromArgv($_SERVER, $_SERVER);
$params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
$params[State::PARAM_MODE] = State::MODE_DEVELOPER;

$bootstrap = Bootstrap::create(BP, $params);
$app = $bootstrap->createApplication('Magento\Tools\SampleData\InstallerApp', ['data' => $data]);
$bootstrap->run($app);
