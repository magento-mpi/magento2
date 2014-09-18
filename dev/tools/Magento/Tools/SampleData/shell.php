<?php

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

$config = include __DIR__ . '/config/config.php';

$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\Tools\SampleData\Installer', ['resources' => $config['setup_resources']]);
