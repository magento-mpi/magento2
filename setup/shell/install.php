<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */



include "../vendor/autoload.php";

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;



/**
 * Parse command line arguments
 */
$currentArgName = false;
$args = array();
foreach ($_SERVER['argv'] as $argNameOrValue) {
    if (substr($argNameOrValue, 0, 2) == '--') {
        // argument name
        $currentArgName = substr($argNameOrValue, 2);
        // in case if argument doesn't need a value
        $args[$currentArgName] = true;
    } else {
        // argument value
        if ($currentArgName) {
            $args[$currentArgName] = $argNameOrValue;
        }
        $currentArgName = false;
    }
}

if (empty($args)) {
    $detailedOptions = array('show_locales', 'show_currencies', 'show_timezones', 'show_install_options');
    echo 'Detailed info:' . PHP_EOL;
    foreach ($detailedOptions as $option) {
        echo '  php -f ' . $_SERVER['argv'][0] . ' -- --' . $option . PHP_EOL;
    }
    echo "  php -f {$_SERVER['argv'][0]} -- [--<install_option_name> \"<option_value>\" ...]\n";

    $exampleOptions = array(
        'license_agreement_accepted' => 'yes',
        'db_host' => 'localhost',
        'db_name' => 'magento',
        'db_user' => 'root',
        'store_url' => 'http://magento.local/',
        'admin_url' => 'http://magento.local/admin',
        'secure_store_url' => 'yes',
        'secure_admin_url' => 'yes',
        'use_rewrites' => 'no',
        'locale' => 'en_US',
        'timezone' => 'America/Los_Angeles',
        'currency' => 'USD',
        'admin_lastname' => 'Smith',
        'admin_firstname' => 'John',
        'admin_email' => 'john.smith@some-email.com',
        'admin_username' => 'admin',
        'admin_password' => '1234qasd',
    );
    echo 'Example of installation:' . PHP_EOL;
    echo '  php -f ' . $_SERVER['argv'][0] . ' --';
    foreach ($exampleOptions as $option => $value) {
        if (!empty($value)) {
            echo ' --' . $option . ' ' . $value;
        } else {
            echo ' --' . $option;
        }
    }
    echo PHP_EOL;
    exit(1);
}

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