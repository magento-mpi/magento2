<?php
/**
 * Entry point for workers in SaaS environment
 *
 * {license_notice}
 *
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
    if (!isset($params[Saas_Saas_Model_EntryPoint_Worker::TASK_OPTIONS_KEY])) {
        throw new LogicException('You have to define task parameters.');
    }
    $appParams[Saas_Saas_Model_EntryPoint_Worker::TASK_OPTIONS_KEY] = $params[
        Saas_Saas_Model_EntryPoint_Worker::TASK_OPTIONS_KEY
    ];

    $entryPoint = new Saas_Saas_Model_EntryPoint_Worker($rootDir, $appParams);
    $entryPoint->processRequest();
};
