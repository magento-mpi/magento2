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

namespace Magento\Log\Model\Resource;

class ShellTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Log\Model\Resource\Shell
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = \Mage::getResourceModel('Magento\Log\Model\Resource\Shell');
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
