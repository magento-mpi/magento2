<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

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
    echo 'Detailed info:' . "\n";
    foreach ($detailedOptions as $option) {
        echo '  php -f ' . $_SERVER['argv'][0] . ' -- --' . $option . "\n";
    }
    echo 'Installation scheme:
  php -f ' . $_SERVER['argv'][0]. ' -- [--<install_option_name> "<option_value>" ...]
Uninstallation:
  php -f ' . $_SERVER['argv'][0] . ' -- --uninstall' . "\n";

    $exampleOptions = array(
        'license_agreement_accepted' => 'yes',
        'locale' => 'en_US',
        'timezone' => '"America/Los_Angeles"',
        'default_currency' => 'USD',
        'db_host' => 'localhost',
        'db_name' => 'magento',
        'db_user' => 'root',
        'url' => '"http://magento.local/"',
        'use_rewrites' => 'no',
        'use_secure_admin' => 'yes',
        'admin_lastname' => 'Smith',
        'admin_firstname' => 'John',
        'admin_email' => '"john.smith@some-email.com"',
        'admin_username' => 'admin',
        'admin_password' => '1234qasd',
        'use_secure' => 'no',
        'secure_base_url' => '"https://magento.local"',
        'cleanup_database' => '',
    );
    echo 'Example of installation:'. "\n";
    echo '  php -f ' . $_SERVER['argv'][0] . ' --';
    foreach ($exampleOptions as $option => $value) {
        if (!empty($value)) {
            echo ' --' . $option . ' ' . $value;
        } else {
            echo ' --' . $option;
        }
    }
    echo "\n";
    exit(1);
}

define('BARE_BOOTSTRAP', 1);
require_once __DIR__ . '/../../app/bootstrap.php';

$entryPoint = new \Magento\App\EntryPoint\EntryPoint(BP);
$entryPoint->run('Magento\Install\App\Console', array('arguments' => $args));