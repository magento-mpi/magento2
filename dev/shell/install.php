<?php
/**
 * {license_notice}
 *
 * @category   Magento
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

define('BARE_BOOTSTRAP', 1);
require_once __DIR__ . '/../../app/bootstrap.php';

$entryPoint = new \Magento\Install\Model\EntryPoint\Console(BP, $args);
$entryPoint->processRequest();
