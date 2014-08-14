<?php
/**
 * Maintenance mode tool
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

define(
    'USAGE',
    "Usage: php -f maintenance.php -- [--set=1|0] [--addresses=127.0.0.1,...'] [--bootstrap=<json>]
        --set - enable or disable maintenance mode
        --addresses - list of allowed IP addresses, comma-separated
        --bootstrap - add or override parameters of the bootstrap\n
    "
);
$opt = getopt('', ['set::', 'addresses::', 'bootstrap::']);

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/../../app/bootstrap.php';
try {
    if (isset($opt['bootstrap'])) {
        $extra = json_decode($opt['bootstrap'], true);
        if (!is_array($extra)) {
            throw new \Exception("Unable to decode JSON in the parameter 'bootstrap'");
        }
        $bootstrap->addParams($extra);
    }
    /** @var \Magento\Framework\App\MaintenanceMode $maintenance */
    $maintenance = $bootstrap->getObjectManager()->get('Magento\Framework\App\MaintenanceMode');
    if (isset($opt['set'])) {
        if (isset($opt['addresses'])) {
            $addresses = empty($opt['addresses']) ? [] : explode(',', $opt['addresses']);
        } else {
            $addresses = null;
        }
        if (1 === (int)$opt['set']) {
            echo "Enabling maintenance mode...\n";
            $maintenance->turnOn($addresses);
        } else {
            echo "Disabling maintenance mode...\n";
            $maintenance->turnOff($addresses);
        }

    } else {
        echo USAGE;
    }
    $info = $maintenance->getStatusInfo();
    if (false === $info) {
        echo "Status: maintenance mode is not active.\n";
    } else {
        $addresses = implode(', ', $info);
        $except = '.';
        if ($addresses) {
            $except = ", except for the HTTP clients from the following IP addresses:\n{$addresses}";
        }
        echo "Status: maintenance mode is active for all entry points{$except}\n";
    }
    exit(0);
} catch (Exception $e) {
    echo $e;
    exit(1);
}
