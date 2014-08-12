<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
use Magento\Framework\App\Filesystem;

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/../app/bootstrap.php';
$params = [
    Filesystem::PARAM_APP_DIRS => [
        Filesystem::PUB_DIR => ['uri' => ''],
        Filesystem::MEDIA_DIR => ['uri' => 'media'],
        Filesystem::STATIC_VIEW_DIR => ['uri' => 'static'],
        Filesystem::UPLOAD_DIR => ['uri' => 'media/upload'],
    ]
];
$bootstrap->addParams($params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
