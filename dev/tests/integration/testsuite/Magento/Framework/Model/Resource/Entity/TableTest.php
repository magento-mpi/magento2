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
namespace Magento\Framework\Model\Resource\Entity;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Resource\Entity\Table
     */
    protected $_model;

    protected function setUp()
    {
        // @codingStandardsIgnoreStart
        $config = new \Magento\Framework\Simplexml\Config();
        $config->table = 'test_table';
        $config->test_key = 'test';
        // @codingStandardsIgnoreEnd

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Framework\Model\Resource\Entity\Table', array('config' => $config));
    }

    public function testGetTable()
    {
        $this->assertEquals('test_table', $this->_model->getTable());
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf('Magento\Framework\Simplexml\Config', $this->_model->getConfig());
        $this->assertEquals('test', $this->_model->getConfig('test_key'));
        $this->assertFalse($this->_model->getConfig('some_key'));
    }
}
