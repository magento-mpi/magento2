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
Magento_Profiler::start('mage');
$params = $_SERVER;
$params[\Magento\Core\Model\App::PARAM_APP_URIS][\Magento\Core\Model\Dir::PUB] = '';
$entryPoint = new \Magento\Core\Model\EntryPoint\Http(new \Magento\Core\Model\Config\Primary(BP, $params));
$entryPoint->processRequest();
Magento_Profiler::stop('mage');
