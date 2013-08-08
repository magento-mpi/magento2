<?php
/**
 * SaaS "Application entry point", required by "SaaS entry point"
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Both "SaaS entry point" and this "Application entry point" have a convention:
 * API consists of one and only one array argument.
 * Underlying implementation of the Application entry point may differ in future versions due to changes
 * in Application itself, but API should remain the same
 *
 * @param array $params
 * @throws LogicException
 */
return function (array $params)
{
    $rootDir = dirname(__DIR__);
    require $rootDir . '/app/bootstrap.php';

    $config = new Saas_Saas_Model_Tenant_Config($rootDir, $params);

    //Process robots.txt request
    if ($_SERVER['REQUEST_URI'] == '/robots.txt') {
        $robotsFile = $config->getMediaDirFile('robots.txt');
        if (!file_exists($robotsFile)) {
            $robotsFile = __DIR__ . '/robots.txt';
        }
        readfile($robotsFile);
        return;
    }
    if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
        /**
         * Hot fix for nginx. Zend_Controller_Request_Http::isSecure
         * works incorrectly in nginx and it leads to infinite loop
         */
        $_SERVER['HTTPS'] = "on";
    }

    $appParams = $config->getApplicationParams();
    Magento_Profiler::start('mage');
    $entryPoint = new Saas_Core_Model_EntryPoint_Http(
        new Magento_Core_Model_Config_Primary($rootDir, $appParams)
    );
    $entryPoint->processRequest();
    Magento_Profiler::stop('mage');
};
