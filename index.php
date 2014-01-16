<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * ...
 * $entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $params);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/app/bootstrap.php';

\Magento\Profiler::start('magento');
$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP, $_SERVER);
$result = $entryPoint->run('Magento\App\Http');
\Magento\Profiler::stop('magento');
return $result;
