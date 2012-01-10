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
php -f install.php --
    --magento-dir="<magento_base_dir>"
    --config-file="<config_php_array_file>"

SYNOPSIS
);
$options = getopt('', array('magento-dir:', 'config-file:'));
if (empty($options['magento-dir']) || empty($options['config-file'])) {
    echo SYNOPSIS;
    exit(1);
}

$configFile = $options['config-file'];
$magentoDir = $options['magento-dir'];
$magentoBootstrapFile = "$magentoDir/app/bootstrap.php";
$magentoBootstrapFile = file_exists($magentoBootstrapFile) ? $magentoBootstrapFile : "$magentoDir/app/Mage.php";

require_once $magentoBootstrapFile;

if (!Mage::isInstalled()) {
    try {
        $config = require($configFile);
        if (!is_array($config) || !isset($config['installer_options'])) {
            throw new UnexpectedValueException("Configuration file '$configFile' is invalid.");
        }
        $installerOptions = $config['installer_options'];
        $systemConfigData = isset($config['config_data']) ? $config['config_data'] : array();
        $installer = new Mage_Install_Model_Installer_Console;
        $isInstalled = $installer->init(Mage::app()) && $installer->setArgs($installerOptions) && $installer->install();
        if (!$isInstalled) {
            throw new Exception(implode(PHP_EOL, $installer->getErrors()));
        }
        $setupModel = new Mage_Core_Model_Resource_Setup('core_setup');
        foreach ($systemConfigData as $configPath => $configValue) {
            $setupModel->setConfigData($configPath, $configValue);
        }
    } catch (Exception $e) {
        echo $e->getMessage() . PHP_EOL . $e->getTraceAsString();
        exit(1);
    }
}
