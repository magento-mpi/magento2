<?php
/**
 * Upgrade command-line script
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
try {
    $params = getopt('', array('local-xml:', 'init-uris:', 'init-dirs:'));
    if (empty($params['local-xml']) || empty($params['init-uris']) || empty($params['init-dirs'])) {
        throw new Exception('Missing required parameters.');
    }
    if (!is_file($params['local-xml'])) {
        throw new Exception("Wrong path to local.xml: '{$params['local-xml']}'");
    }
} catch (Exception $e) {
    echo 'USAGE:
    php -f upgrade.php -- --local-xml=<path_to_file>
        --init-uris=<serialized_base64_encoded_array>
        --init-dirs=<serialized_base64_encoded_array>
    ';
    echo $e;
    exit(1);
}
define('BARE_BOOTSTRAP', 1);
require __DIR__ . '/../../../app/bootstrap.php';
Mage::setIsDeveloperMode(true);
Mage::app(array(
    Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA => file_get_contents($params['local-xml']),
    Mage_Core_Model_App::INIT_OPTION_URIS => unserialize(base64_decode($params['init-uris'])),
    Mage_Core_Model_App::INIT_OPTION_DIRS => unserialize(base64_decode($params['init-dirs'])),
));
Mage_Core_Model_Resource_Setup::applyAllUpdates();
