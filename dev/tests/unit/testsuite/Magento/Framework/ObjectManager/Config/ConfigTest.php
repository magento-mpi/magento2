<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArgumentsEmpty()
    {
        $config = new Config();
        $this->assertSame(array(), $config->getArguments('An invalid type'));
    }

    public function testExtendMergeConfiguration()
    {
        $this->_assertFooTypeArguments(new Config());
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
        $definitions = $this->getMock('Magento\Framework\ObjectManager\DefinitionInterface');
        $definitions->expects($this->once())->method('getClasses')->will($this->returnValue(array('FooType')));

        $cache = $this->getMock('Magento\Framework\ObjectManager\ConfigCacheInterface');
        $cache->expects($this->once())->method('get')->will($this->returnValue(false));

        $config = new Config(null, $definitions);
        $config->setCache($cache);

        $this->_assertFooTypeArguments($config);
    }

    public function testGetPreferenceTrimsFirstSlash()
    {
        $config = new Config();
        $this->assertEquals('Some\Class\Name', $config->getPreference('\Some\Class\Name'));
    }

    public function testExtendIgnoresFirstSlashesOnPreferences()
    {
        $config = new Config();
        $config->extend(array('preferences' => array('\Some\Interface' => '\Some\Class')));
        $this->assertEquals('Some\Class', $config->getPreference('Some\Interface'));
        $this->assertEquals('Some\Class', $config->getPreference('\Some\Interface'));
    }

    public function testExtendIgnoresFirstShashesOnVirtualTypes()
    {
        $config = new Config();
        $config->extend(array('\SomeVirtualType' => array('type' => '\Some\Class')));
        $this->assertEquals('Some\Class', $config->getInstanceType('SomeVirtualType'));
    }

    public function testExtendIgnoresFirstShashes()
    {
        $config = new Config();
        $config->extend(array('\Some\Class' => array('arguments' => array('someArgument'))));
        $this->assertEquals(array('someArgument'), $config->getArguments('Some\Class'));
    }

    public function testExtendIgnoresFirstShashesForSharing()
    {
        $config = new Config();
        $config->extend(array('\Some\Class' => array('shared' => true)));
        $this->assertTrue($config->isShared('Some\Class'));
    }
}
