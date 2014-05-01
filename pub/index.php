<?php
/**
 * Public alias for the application entry point
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/../app/bootstrap.php';
$params = $_SERVER;
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::PUB_DIR] = array('uri' => '');
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::PUB_LIB_DIR] = array('uri' => 'lib');
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::MEDIA_DIR] = array('uri' => 'media');
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR] = array('uri' => 'static');
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::PUB_VIEW_CACHE_DIR] = array('uri' => 'cache');
$params[\Magento\Framework\App\Filesystem::PARAM_APP_DIRS][\Magento\Framework\App\Filesystem::UPLOAD_DIR] = array('uri' => 'media/upload');
$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\Framework\App\Http');
