<?php
/**
 * Entry point for processing tmt requests in SaaS environment
 *
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas
 * @copyright  {copyright}
 * @license    {license_link}
 */
return function (array $params)
{
    $rootDir = dirname(__DIR__);
    require $rootDir . '/app/bootstrap.php';

    $config = new Saas_Saas_Model_Tenant_Config($rootDir, $params['config']);
    $appParams = $config->getApplicationParams();
    $appParams[Mage::PARAM_MODE] = Mage_Core_Model_App_State::MODE_PRODUCTION;

    $configPrimary = new Mage_Core_Model_Config_Primary($rootDir, $appParams);

    $entryPoint = new Saas_Core_Model_EntryPoint_Tmt($configPrimary, null, $params);
    $entryPoint->processRequest();
};

