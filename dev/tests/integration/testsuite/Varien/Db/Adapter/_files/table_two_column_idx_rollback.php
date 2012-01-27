<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Db
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = new Mage_Core_Model_Resource_Setup(Mage_Core_Model_Resource_Setup::DEFAULT_SETUP_CONNECTION);
$connection = $installer->getConnection();

$tableName = '_two_column_idx';
$connection->dropTable($tableName);
