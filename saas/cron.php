<?php
/**
 * Entry point for scheduled(cron) jobs in SaaS environment
 *
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * @param array $params
 * @throws LogicException
 */
return function (array $params)
{
    $rootDir = dirname(__DIR__);
    require $rootDir . '/app/bootstrap.php';

    $config = new Saas_Saas_Model_Tenant_Config($rootDir, $params);
    $appParams = $config->getApplicationParams();

    $entryPoint = new Magento_Core_Model_EntryPoint_Cron(
        new Magento_Core_Model_Config_Primary($rootDir, $appParams)
    );
    $entryPoint->processRequest();
};
