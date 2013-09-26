<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    system_configuration
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once __DIR__ . '/../../../../../app/bootstrap.php';
$rootDir = realpath(__DIR__ . '/../../../../..');
try {
    $config = new \Magento\Core\Model\Config\Primary($rootDir, array());
    $entryPoint = new \Magento\Core\Model\EntryPoint\Cron($config);

    /** @var $configModel \Magento\Core\Model\Config */
    $configModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config');
    $configModel->removeCache();
    $configModel->reinit();
    $config = array();

    foreach (glob(__DIR__ . '/AliasesMap/cms_content_tables_*.php', GLOB_BRACE) as $configFile) {
        $config = array_merge($config, include($configFile));
    }

    foreach ($config as $table => $field) {
        updateFieldForTable($table, $field);
    }
} catch (\Exception $e) {
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
    $installer = \Mage::getResourceModel('Mage_Core_Model_Resource_Setup', array('resourceName' => 'core_setup'));
    $installer->startSetup();

    $table = $installer->getTable($table);
    print '-----' . "\n";
    if ($installer->getConnection()->isTableExists($table)) {
        print 'Table `' . $table . "` processed\n";

        $indexList = $installer->getConnection()->getIndexList($table);
        $pkField = array_shift($indexList[$installer->getConnection()->getPrimaryKeyName($table)]['fields']);
        /** @var $select \Magento\Db\Select */
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
