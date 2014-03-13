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
    $entryPoint = new \Magento\App\EntryPoint\EntryPoint($rootDir, array());

    $objectManager = new \Magento\App\ObjectManager();
    /** @var $configModel \Magento\App\Config\ReinitableConfigInterface */
    $configModel = $objectManager->get('Magento\App\Config\ReinitableConfigInterface');
    $configModel->reinit();
    $config = array();

    foreach (glob(__DIR__ . '/AliasesMap/cms_content_tables_*.php', GLOB_BRACE) as $configFile) {
        $config = array_merge($config, include($configFile));
    }

    foreach ($config as $table => $field) {
        updateFieldForTable($objectManager, $table, $field);
    }
} catch (\Exception $e) {
    echo "Make sure that you launch this script with Magento 2 configured sources. \n\n";
    echo $e->getMessage();
}

/**
 * Replace {{skin url=""}} with {{view url=""}} for given table field
 *
 * @param \Magento\ObjectManager $objectManager
 * @param string $table
 * @param string $col
 */
function updateFieldForTable($objectManager, $table, $col)
{
    /** @var $installer \Magento\Core\Model\Resource\Setup */
    $installer = $objectManager->create('Magento\Core\Model\Resource\Setup', array('resourceName' => 'core_setup'));
    $installer->startSetup();

    $table = $installer->getTable($table);
    echo '-----' . "\n";
    if ($installer->getConnection()->isTableExists($table)) {
        echo 'Table `' . $table . "` processed\n";

        $indexList = $installer->getConnection()->getIndexList($table);
        $pkField = array_shift($indexList[$installer->getConnection()->getPrimaryKeyName($table)]['fields']);
        /** @var $select \Magento\Db\Select */
        $select = $installer->getConnection()->select()->from($table, array('id' => $pkField, 'content' => $col));
        $result = $installer->getConnection()->fetchPairs($select);

        echo 'Records count: ' . count($result) . ' in table: `' . $table . "`\n";

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
        echo 'Table `' . $table . "` was not found\n";
    }
    $installer->endSetup();
    echo '-----' . "\n";
}

/**
 * Print array of messages
 *
 * @param array $logMessages
 */
function printLog($logMessages)
{
    foreach ($logMessages as $stringsArray) {
        echo "\n";
        echo implode("\n", $stringsArray);
        echo "\n";
    }
}
