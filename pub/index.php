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
$params[\Magento\Filesystem::PARAM_APP_DIRS][\Magento\Filesystem::PUB] = array('uri' => '');
$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $params);
$entryPoint->run('Magento\App\Http');
