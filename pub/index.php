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
\Magento\Profiler::start('magento');
$params = $_SERVER;
$params[\Magento\App\Filesystem::PARAM_APP_DIRS][\Magento\App\Filesystem::PUB_DIR] = array('uri' => '');
$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $params);
$result = $entryPoint->run('Magento\App\Http');
\Magento\Profiler::stop('magento');
return $result;
