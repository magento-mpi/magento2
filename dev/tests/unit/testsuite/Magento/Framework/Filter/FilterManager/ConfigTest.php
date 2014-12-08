<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filter\FilterManager;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Filter\FilterManager\Config
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = new \Magento\Framework\Filter\FilterManager\Config(['test' => 'test']);
    }

    public function testGetFactories()
    {
        $expectedConfig = [
            'test' => 'test',
            'Magento\Framework\Filter\Factory',
            'Magento\Framework\Filter\ZendFactory',
        ];
        $this->assertEquals($expectedConfig, $this->_config->getFactories());
    }
}
