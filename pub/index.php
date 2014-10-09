<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\Filesystem\DirectoryList;

require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$params[DirectoryList::INIT_PARAM_PATHS] = [
    DirectoryList::PUB => ['uri' => ''],
    DirectoryList::MEDIA => ['uri' => 'media'],
    DirectoryList::STATIC_VIEW => ['uri' => 'static'],
    DirectoryList::UPLOAD => ['uri' => 'media/upload'],
];
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
