<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\Frontend;

class PoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Cache\Frontend\Pool
     */
    protected $_model;

    /**
     * Array of frontend cache instances stubs, used to verify, what is stored inside the pool
     *
     * @var \PHPUnit_Framework_MockObject_MockObject[]
     */
    protected $_frontendInstances = array();

    protected function setUp()
    {
        $this->_frontendInstances = array(
            Pool::DEFAULT_FRONTEND_ID => $this->getMock('Magento\Cache\FrontendInterface'),
            'resource1' => $this->getMock('Magento\Cache\FrontendInterface'),
            'resource2' => $this->getMock('Magento\Cache\FrontendInterface'),
        );

        $frontendFactoryMap = array(
            array(
                array('data1' => 'value1', 'data2' => 'value2'), $this->_frontendInstances[Pool::DEFAULT_FRONTEND_ID]
            ),
            array(array('r1d1' => 'value1', 'r1d2' => 'value2'), $this->_frontendInstances['resource1']),
            array(array('r2d1' => 'value1', 'r2d2' => 'value2'), $this->_frontendInstances['resource2']),
        );
        $frontendFactory = $this->getMock('Magento\App\Cache\Frontend\Factory', array(), array(), '', false);
        $frontendFactory->expects($this->any())->method('create')->will($this->returnValueMap($frontendFactoryMap));

        $arguments = $this->getMock('Magento\App\Arguments', array(), array(), '', false);
        $arguments->expects($this->any())->method('getCacheFrontendSettings')->will($this->returnValue(array(
            'resource2' => array('r2d1' => 'value1', 'r2d2' => 'value2'),
        )));

        $frontendSettings = array(
            Pool::DEFAULT_FRONTEND_ID => array('data1' => 'value1', 'data2' => 'value2'),
            'resource1' => array('r1d1' => 'value1', 'r1d2' => 'value2'),
        );

        $this->_model = new \Magento\App\Cache\Frontend\Pool($arguments, $frontendFactory, $frontendSettings);
    }

    /**
     * Test that constructor delays object initialization (does not perform any initialization of its own)
     */
    public function testConstructorNoInitialization()
    {
        $arguments = $this->getMock('Magento\App\Arguments', array(), array(), '', false);
        $frontendFactory = $this->getMock('Magento\App\Cache\Frontend\Factory', array(), array(), '', false);
        $frontendFactory
            ->expects($this->never())
            ->method('create')
        ;
        new \Magento\App\Cache\Frontend\Pool($arguments, $frontendFactory);
    }

    /**
     * @param array $fixtureCacheConfig
     * @param array $frontendSettings
     * @param array $expectedFactoryArg
     *
     * @dataProvider initializationParamsDataProvider
     */
    public function testInitializationParams(
        array $fixtureCacheConfig, array $frontendSettings, array $expectedFactoryArg
    ) {
        $arguments = $this->getMock('Magento\App\Arguments', array(), array(), '', false);
        $arguments
            ->expects($this->once())->method('getCacheFrontendSettings')->will($this->returnValue($fixtureCacheConfig));

        $frontendFactory = $this->getMock('Magento\App\Cache\Frontend\Factory', array(), array(), '', false);
        $frontendFactory->expects($this->at(0))->method('create')->with($expectedFactoryArg);

        $model = new \Magento\App\Cache\Frontend\Pool($arguments, $frontendFactory, $frontendSettings);
        $model->current();
    }

    public function initializationParamsDataProvider()
    {
        return array(
            'default frontend, default settings' => array(
                array(),
                array(Pool::DEFAULT_FRONTEND_ID => array('default_option' => 'default_value')),
                array('default_option' => 'default_value'),
            ),
            'default frontend, overridden settings' => array(
                array(Pool::DEFAULT_FRONTEND_ID => array('configured_option' => 'configured_value')),
                array(Pool::DEFAULT_FRONTEND_ID => array('ignored_option' => 'ignored_value')),
                array('configured_option' => 'configured_value'),
            ),
            'custom frontend, default settings' => array(
                array(),
                array('custom' => array('default_option' => 'default_value')),
                array('default_option' => 'default_value'),
            ),
            'custom frontend, overridden settings' => array(
                array('custom' => array('configured_option' => 'configured_value')),
                array('custom' => array('ignored_option' => 'ignored_value')),
                array('configured_option' => 'configured_value'),
            ),
        );
    }

    public function testCurrent()
    {
        $this->assertSame($this->_frontendInstances[Pool::DEFAULT_FRONTEND_ID], $this->_model->current());
    }

    public function testKey()
    {
        $this->assertEquals(Pool::DEFAULT_FRONTEND_ID, $this->_model->key());
    }

    public function testNext()
    {
        $this->assertEquals(Pool::DEFAULT_FRONTEND_ID, $this->_model->key());

        $this->_model->next();
        $this->assertEquals('resource1', $this->_model->key());
        $this->assertSame($this->_frontendInstances['resource1'], $this->_model->current());

        $this->_model->next();
        $this->assertEquals('resource2', $this->_model->key());
        $this->assertSame($this->_frontendInstances['resource2'], $this->_model->current());

        $this->_model->next();
        $this->assertNull($this->_model->key());
        $this->assertFalse($this->_model->current());
    }

    public function testRewind()
    {
        $this->_model->next();
        $this->assertNotEquals(Pool::DEFAULT_FRONTEND_ID, $this->_model->key());

        $this->_model->rewind();
        $this->assertEquals(Pool::DEFAULT_FRONTEND_ID, $this->_model->key());
    }

    public function testValid()
    {
        $this->assertTrue($this->_model->valid());

        $this->_model->next();
        $this->assertTrue($this->_model->valid());

        $this->_model->next();
        $this->_model->next();
        $this->assertFalse($this->_model->valid());

        $this->_model->rewind();
        $this->assertTrue($this->_model->valid());
    }

    public function testGet()
    {
        foreach ($this->_frontendInstances as $frontendId => $frontendInstance) {
            $this->assertSame($frontendInstance, $this->_model->get($frontendId));
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cache frontend 'unknown' is not recognized
     */
    public function testGetUnknownFrontendId()
    {
        $this->_model->get('unknown');
    }
}
