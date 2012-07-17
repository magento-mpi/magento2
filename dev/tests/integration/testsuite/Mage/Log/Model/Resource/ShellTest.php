<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Log
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Log_Model_Resource_ShellTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Log_Model_Resource_Shell
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Log_Model_Resource_Shell;
    }

    protected function tearDown()
    {
        $this->_model = null;
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
