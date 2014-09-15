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
    "Usage: php -f maintenance.php -- [--set=1|0] [--addresses=127.0.0.1,...|none'] [--bootstrap=<json>]
        --set - enable or disable maintenance mode
        --addresses - list of allowed IP addresses, comma-separated
        --bootstrap - add or override parameters of the bootstrap\n"
);
$opt = getopt('', ['set::', 'addresses::', 'bootstrap::']);
if (empty($opt)) {
    echo USAGE;
}

require __DIR__ . '/../../app/bootstrap.php';
try {
    $params = $_SERVER;
    if (isset($opt['bootstrap'])) {
        $extra = json_decode($opt['bootstrap'], true);
        if (!is_array($extra)) {
            throw new \Exception("Unable to decode JSON in the parameter 'bootstrap'");
        }
        $params = array_replace_recursive($params, $extra);
    }
    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
    /** @var \Magento\Framework\App\MaintenanceMode $maintenance */
    $maintenance = $bootstrap->getObjectManager()->get('Magento\Framework\App\MaintenanceMode');
    if (isset($opt['set'])) {
        if (1 === (int)$opt['set']) {
            echo "Enabling maintenance mode...\n";
            $maintenance->set(true);
        } else {
            echo "Disabling maintenance mode...\n";
            $maintenance->set(false);
        }
    }
    if (isset($opt['addresses'])) {
        $addresses = ('none' == $opt['addresses']) ? '' : $opt['addresses'];
        $maintenance->setAddresses($addresses);
    }
    echo 'Status: maintenance mode is ' . ($maintenance->isOn() ? 'active' : 'not active') . ".\n";
    $addresses = implode(', ', $maintenance->getAddressInfo());
    echo "List of exempt IP-addresses:";
    if ($addresses) {
        echo " {$addresses}\n";
    } else {
        echo " none.\n";
    }
    exit(0);
} catch (Exception $e) {
    echo $e;
    exit(1);
}
