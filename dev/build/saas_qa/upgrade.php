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
    $params = getopt('', array('local-xml:', 'media-uri:', 'media-dir:', 'var-dir:'));
    if (empty($params['local-xml']) || empty($params['media-uri']) || empty($params['media-dir'])
        || empty($params['var-dir'])
    ) {
        throw new Exception('Missing required parameters.');
    }
    if (!is_file($params['local-xml']) || !is_readable($params['local-xml'])) {
        throw new Exception("Wrong path to local.xml or file is not readable: '{$params['local-xml']}'");
    }
} catch (Exception $e) {
    echo 'USAGE:
    php -f upgrade.php --
        --local-xml=<path_to_file>
        --media-uri=<uri>
        --media-dir=<absolute_path>
        --var-dir=<absolute_path>
    ';
    echo $e;
    exit(1);
}
define('BARE_BOOTSTRAP', 1);
require __DIR__ . '/../../../app/bootstrap.php';
Mage::setIsDeveloperMode(true);
Mage::app(array(
    Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA => file_get_contents($params['local-xml']),
    Mage_Core_Model_App::INIT_OPTION_URIS => array(Mage_Core_Model_Dir::MEDIA => $params['media-uri']),
    Mage_Core_Model_App::INIT_OPTION_DIRS => array(
        Mage_Core_Model_Dir::MEDIA => $params['media-dir'],
        Mage_Core_Model_Dir::VAR_DIR => $params['var-dir'],
    ),
));
Mage_Core_Model_Resource_Setup::applyAllUpdates();
