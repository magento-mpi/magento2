<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

include "vendor/autoload.php";

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

$configuration = include "config/application.config.php";

$smConfig = new ServiceManagerConfig();
$serviceManager = new ServiceManager($smConfig);
$serviceManager->setService('ApplicationConfig', $configuration);

$serviceManager->setAllowOverride(true);
$serviceManager->get('ModuleManager')->loadModules();
$serviceManager->setAllowOverride(false);

$serviceManager->get('Application')
    ->bootstrap()
    ->run();