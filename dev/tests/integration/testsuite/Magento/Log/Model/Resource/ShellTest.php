<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Log
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Log_Model_Resource_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Log_Model_Resource_Shell
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Log_Model_Resource_Shell');
    }

    public function testGetTablesInfo()
    {
        $tables = $this->_model->getTablesInfo();
        $this->assertNotEmpty($tables);

        $sample = current($tables);
        $requiredKeys = array('name', 'rows', 'data_length', 'index_length');
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $sample);
        }
    }
}
