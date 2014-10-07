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
use Magento\Framework\App\Filesystem\DirectoryList;

require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$params[DirectoryList::PARAM_APP_DIRS] = [
    DirectoryList::PUB_DIR => ['uri' => ''],
    DirectoryList::MEDIA_DIR => ['uri' => 'media'],
    DirectoryList::STATIC_VIEW_DIR => ['uri' => 'static'],
    DirectoryList::UPLOAD_DIR => ['uri' => 'media/upload'],
];
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
