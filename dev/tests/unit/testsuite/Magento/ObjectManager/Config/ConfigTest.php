<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArgumentsEmpty()
    {
        $config = new Config;
        $this->assertSame(array(), $config->getArguments('An invalid type'));
    }

    public function testExtendMergeConfiguration()
    {
        $this->_assertFooTypeArguments(new Config);
    }

    /**
     * A primitive fixture for testing merging arguments
     *
     * @param Config $config
     */
    private function _assertFooTypeArguments(Config $config)
    {
        $expected = array('argName' => 'argValue');
        $fixture = array('FooType' => array('arguments' => $expected));
        $config->extend($fixture);
        $this->assertEquals($expected, $config->getArguments('FooType'));
    }

    public function testExtendWithCacheMock()
    {
        $definitions = $this->getMockForAbstractClass('\Magento\ObjectManager\Definition');
        $definitions->expects($this->once())->method('getClasses')->will($this->returnValue(array('FooType')));

        $cache = $this->getMockForAbstractClass('\Magento\ObjectManager\ConfigCache');
        $cache->expects($this->once())->method('get')->will($this->returnValue(false));

        $config = new Config(null, $definitions);
        $config->setCache($cache);

        $this->_assertFooTypeArguments($config);
    }
} 
