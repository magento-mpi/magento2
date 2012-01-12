<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  runner
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('SYNOPSIS', <<<SYNOPSIS
php -f install.php -- --magento-dir="<magento_base_dir>" --config-file="<config_php_array_file>"
php -f install.php -- --magento-dir="<magento_base_dir>" --uninstall

SYNOPSIS
);
$options = getopt('', array('magento-dir:', 'config-file:', 'uninstall'));
if (empty($options['magento-dir']) || (empty($options['config-file']) && !isset($options['uninstall']))) {
    echo SYNOPSIS;
    var_dump($options);
    exit(1);
}

$configFile = isset($options['config-file']) ? $options['config-file'] : null;
$magentoDir = $options['magento-dir'];
$isUninstallMode = isset($options['uninstall']);
$magentoBootstrapFile = "$magentoDir/app/bootstrap.php";
$magentoBootstrapFile = file_exists($magentoBootstrapFile) ? $magentoBootstrapFile : "$magentoDir/app/Mage.php";

require_once $magentoBootstrapFile;

function installMagentoApplication(array $installerOptions, array $systemConfigData = array())
{
    if (Mage::isInstalled()) {
        return;
    }
    $installer = new Mage_Install_Model_Installer_Console;
    $isInstalled = $installer->init(Mage::app()) && $installer->setArgs($installerOptions) && $installer->install();
    if (!$isInstalled) {
        throw new Exception(implode(PHP_EOL, $installer->getErrors()));
    }
    $setupModel = new Mage_Core_Model_Resource_Setup(Mage_Core_Model_Resource_Setup::DEFAULT_SETUP_CONNECTION);
    foreach ($systemConfigData as $configPath => $configValue) {
        $setupModel->setConfigData($configPath, $configValue);
    }

    /* Prepare order autoincrement */
    $select = $setupModel->getConnection()->select()
        ->from($setupModel->getTable('eav_entity_type'), 'entity_type_id')
        ->where('entity_type_code=?', 'order');
    $data = array(
        'entity_type_id' => $setupModel->getConnection()->fetchOne($select),
        'store_id' => '1',
        /*Paypal has limitation for order number (20 characters). 10 digits prefix + 8 digits number is good enough */
        'increment_prefix' => time(),
    );
    $setupModel->getConnection()->insert($setupModel->getTable('eav_entity_store'), $data);
}

function uninstallMagentoApplication()
{
    if (!Mage::isInstalled()) {
        return;
    }
    Mage::init();
    $dbConfig = Mage::getConfig()->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
    if ($dbConfig->model != 'mysql4') {
        throw new UnexpectedValueException('Database uninstall is supported for the MySQL only.');
    }
    $resourceModel = new Mage_Core_Model_Resource;
    $dbConnection = $resourceModel->getConnection(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
    $dbConnection->query("DROP DATABASE `$dbConfig->dbname`");
    $dbConnection->query("CREATE DATABASE `$dbConfig->dbname`");
    unlink(Mage::app()->getConfig()->getOptions()->getEtcDir() . '/local.xml');
    Varien_Io_File::rmdirRecursive(Mage::app()->getConfig()->getOptions()->getCacheDir());
}

try {
    if ($isUninstallMode) {
        uninstallMagentoApplication();
    } else {
        $config = require($configFile);
        if (!is_array($config) || !isset($config['installer_options'])) {
            throw new UnexpectedValueException("Configuration file '$configFile' is invalid.");
        }
        $installerOptions = $config['installer_options'];
        $systemConfigData = isset($config['config_data']) ? $config['config_data'] : array();
        installMagentoApplication($installerOptions, $systemConfigData);
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL . $e->getTraceAsString();
    exit(1);
}
