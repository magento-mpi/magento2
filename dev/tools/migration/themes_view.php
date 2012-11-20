<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    system_configuration
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Constants definition
 */
define('DS', DIRECTORY_SEPARATOR);
define('BP', realpath(__DIR__ . '/../../..'));
/**
 * Require necessary files
 */
require_once BP . '/lib/Magento/Autoload.php';
require_once BP . '/app/code/core/Mage/Core/functions.php';
require_once BP . '/app/Mage.php';

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';
Magento_Autoload::getInstance()->addIncludePath($paths);
Mage::setRoot();
Mage::setIsDeveloperMode(true);

try {
    Mage::getConfig()->cleanCache();
    Mage::getConfig()->reinit();
    $config = array();

    foreach (glob(dirname(__FILE__) . '/aliases_map/cms_content_tables_*.php', GLOB_BRACE) as $configFile) {
        $config = array_merge($config, include($configFile));
    }

    foreach ($config as $table => $field) {
        updateFieldForTable($table, $field);
    }
} catch (Exception $e) {
    echo "Make sure that you launch this script with Magento 2 configured sources. \n\n";
    echo $e->getMessage();
}

/**
 * Replace {{skin url=""}} with {{view url=""}} for given table field
 *
 * @param string $table
 * @param string $col
 */
function updateFieldForTable($table, $col)
{
    /** @var $installer Mage_Core_Model_Resource_Setup */
    $installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup', array('resourceName' => 'core_setup'));
    $installer->startSetup();

    $table = $installer->getTable($table);
    print '-----' . "\n";
    if ($installer->getConnection()->isTableExists($table)) {
        print 'Table `' . $table . "` processed\n";

        $indexList = $installer->getConnection()->getIndexList($table);
        $pkField = array_shift($indexList[$installer->getConnection()->getPrimaryKeyName($table)]['fields']);
        /** @var $select Varien_Db_Select */
        $select = $installer->getConnection()->select()->from($table, array('id' => $pkField, 'content' => $col));
        $result = $installer->getConnection()->fetchPairs($select);

        print 'Records count: ' . count($result) . ' in table: `' . $table . "`\n";

        $logMessages = array();
        foreach ($result as $recordId => $string) {
            $content = str_replace('{{skin', '{{view', $string, $count);
            if ($count) {
                $installer->getConnection()->update($table, array($col => $content),
                    $installer->getConnection()->quoteInto($pkField . '=?', $recordId));
                $logMessages['replaced'][] = 'Replaced -- Id: ' . $recordId . ' in table `' . $table . '`';
            } else {
                $logMessages['skipped'][] = 'Skipped -- Id: ' . $recordId . ' in table `' . $table . '`';
            }
        }
        if (count($result)) {
            printLog($logMessages);
        }
    } else {
        print 'Table `' . $table . "` was not found\n";
    }
    $installer->endSetup();
    print '-----' . "\n";
}

/**
 * Print array of messages
 *
 * @param array $logMessages
 */
function printLog($logMessages)
{
    foreach ($logMessages as $stringsArray) {
        print "\n";
        print implode("\n", $stringsArray);
        print "\n";
    }
}
