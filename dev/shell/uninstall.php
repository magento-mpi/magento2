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

require __DIR__ . '/../../app/bootstrap.php';
try {
    $extra = [];
    if (isset($opt['bootstrap'])) {
        $extra = json_decode($opt['bootstrap'], true);
        if (!is_array($extra)) {
            throw new \Exception("Unable to decode JSON in the parameter 'bootstrap'");
        }
    }
    $bootstrap = new \Magento\Framework\App\Bootstrap(BP, $_SERVER, $extra);
    $log = new  \Zend_Log(new \Zend_Log_Writer_Stream('php://stdout'));
    /** @var \Magento\Install\Model\Uninstaller $uninstall */
    $uninstall = $bootstrap->getObjectManager()->create('\Magento\Install\Model\Uninstaller', ['log' => $log]);
    $uninstall->uninstall();
    exit(0);
} catch (\Exception $e) {
    echo $e;
    exit(1);
}
