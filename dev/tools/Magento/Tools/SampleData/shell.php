<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Framework\App\State;
use Magento\Framework\App\Filesystem;
use Magento\Store\Model\StoreManager;

require_once __DIR__ . '/../../../../../app/bootstrap.php';
require __DIR__ . '/../../../bootstrap.php';
defined('BARE_BOOTSTRAP')
    || define('BARE_BOOTSTRAP', true);
umask(0);

$params = array(
    State::PARAM_MODE => State::MODE_DEVELOPER,
);

$config = [];
foreach (glob(__DIR__ . '/config/*.php') as $filename) {
    if (is_file($filename)) {
        $configPart = include $filename;
        $config = array_merge_recursive($config, $configPart);
    }
}

$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
$app = $bootstrap->createApplication('Magento\Tools\SampleData\Installer', ['resources' => $config['setup_resources']]);
$bootstrap->run($app);
