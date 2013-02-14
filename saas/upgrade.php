<?php
/**
 * Entry point for upgrading application in SaaS environment
 *
 * Interface:
 * - SaaS infrastructure includes this file and executes as a callback
 * - The callback receives one argument: an array with various application configuration, adjusted for the tenant
 */
/**
 * @param array $tenantData
 */
return function (array $tenantData)
{
    $rootDir = dirname(__DIR__);
    require $rootDir . '/app/bootstrap.php';
    $config = new Saas_Saas_Model_Tenant_Config($rootDir, $tenantData);
    $entryPoint = new Mage_Install_Model_EntryPoint_Upgrade($rootDir, $config->getApplicationParams($rootDir));
    $entryPoint->processRequest();
};
