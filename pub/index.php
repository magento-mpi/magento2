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
$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\Framework\App\Http');
