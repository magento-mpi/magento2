<?php
/**
 * Uninstall utility
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('USAGE', "Usage: php -f uninstall.php -- [--bootstrap=<json>]\n");
$opt = getopt('', ['bootstrap::']);

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
    /** @var \Magento\Install\Model\Uninstaller $uninstall */
    $uninstall = $bootstrap->getObjectManager()->create('\Magento\Install\Model\Uninstaller');
    $uninstall->uninstall();
    exit(0);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
