<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('SYNOPSIS', <<<SYNOPSIS
php -f install.php -- --show_locales
php -f install.php -- --show_currencies
php -f install.php -- --show_timezones
php -f install.php -- --show_install_options
php -f install.php -- [--<install_option_name> "<option_value>", ...]
php -f install.php -- --uninstall

SYNOPSIS
);

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
    echo SYNOPSIS;
    exit(1);
}

require_once __DIR__ . '/../../app/bootstrap.php';

try {
    $installer = new Mage_Install_Model_Installer_Console();
    if (isset($args['show_locales'])) {
        var_export($installer->getAvailableLocales());
    } else if (isset($args['show_currencies'])) {
        var_export($installer->getAvailableCurrencies());
    } else if (isset($args['show_timezones'])) {
        var_export($installer->getAvailableTimezones());
    } else if (isset($args['show_install_options'])) {
        var_export($installer->getAvailableInstallOptions());
    } else {
        if (isset($args['uninstall'])) {
            $installer->uninstall();
            echo 'Uninstalled successfully' . PHP_EOL;
        } else {
            $encryptionKey = $installer->install($args);
            if ($encryptionKey) {
                echo 'Installed successfully, encryption key: ' . $encryptionKey . PHP_EOL;
            }
        }
        if ($installer->hasErrors()) {
            throw new Exception(implode(PHP_EOL, $installer->getErrors()));
        }
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
