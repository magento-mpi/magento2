<?php
/**
 * {license_notice}
 *
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
    echo 'Detailed info:' . PHP_EOL;
    foreach ($detailedOptions as $option) {
        echo '  php -f ' . $_SERVER['argv'][0] . ' -- --' . $option . PHP_EOL;
    }
    echo <<<INSTALLSCHEME
Installation scheme:
  php -f {$_SERVER['argv'][0]}  -- [--<install_option_name> "<option_value>" ...]
Uninstallation:
  php -f  {$_SERVER['argv'][0]} -- --uninstall

INSTALLSCHEME;

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
        'cleanup_database' => ''
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

define('BARE_BOOTSTRAP', 1);
require_once __DIR__ . '/../../app/bootstrap.php';

$result = is_writable(BP . '/app/etc');
if ($result === null) {
    echo 'error: Can\'t access directory "app/etc".' . PHP_EOL;
    exit(1);
} elseif ($result === false) {
    echo 'error: Path "app/etc" must be writable.' . PHP_EOL;
    exit(1);
}

if (file_exists(BP . '/var') && !is_writable(BP . '/var')) {
    echo 'error: Path "var" must be writable.' . PHP_EOL;
    exit(1);
}

$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Magento\Install\App\Console', array('arguments' => $args));
