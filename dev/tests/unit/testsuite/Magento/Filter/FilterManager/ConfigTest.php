<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filter\FilterManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filter\FilterManager\Config
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = new \Magento\Filter\FilterManager\Config(array('test' => 'test'));
    }

    public function testGetFactories()
    {
        $expectedConfig = array('test' => 'test', 'Magento\Filter\Factory', 'Magento\Filter\ZendFactory');
        $this->assertEquals($expectedConfig, $this->_config->getFactories());
    }
}
