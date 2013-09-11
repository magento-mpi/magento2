<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Entity_TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Entity\Table
     */
    protected $_model;

    protected function setUp()
    {
        // @codingStandardsIgnoreStart
        $config = new \Magento\Simplexml\Config();
        $config->table      = 'test_table';
        $config->test_key   = 'test';
        // @codingStandardsIgnoreEnd

        $this->_model = Mage::getResourceModel('\Magento\Core\Model\Resource\Entity\Table', array('config' => $config));
    }

    public function testGetTable()
    {
        $this->assertEquals('test_table', $this->_model->getTable());
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf('\Magento\Simplexml\Config', $this->_model->getConfig());
        $this->assertEquals('test', $this->_model->getConfig('test_key'));
        $this->assertFalse($this->_model->getConfig('some_key'));
    }
}
