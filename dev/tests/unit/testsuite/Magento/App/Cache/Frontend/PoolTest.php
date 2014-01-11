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
     */
    protected $_frontendInstances = array();

    protected function setUp()
    {
        $config = $this->getMock('Magento\App\Config', array(), array(), '', false);
        $config->expects($this->any())->method('getCacheSettings')->will($this->returnValue(array()));

        $frontendFactory = $this->getMock('Magento\App\Cache\Frontend\Factory', array(), array(), '', false);

        $this->_frontendInstances = array(
            \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID
            => $this->getMock('Magento\Cache\FrontendInterface'),
            'resource1' => $this->getMock('Magento\Cache\FrontendInterface'),
            'resource2' => $this->getMock('Magento\Cache\FrontendInterface'),
        );
        $frontendFactory->expects($this->any())
            ->method('create')
            ->will(
                $this->returnValueMap(array(
                    array(
                        array('data1' => 'value1', 'data2' => 'value2'),
                        $this->_frontendInstances[\Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID]
                    ),
                    array(array('r1d1' => 'value1', 'r1d2' => 'value2'), $this->_frontendInstances['resource1']),
                    array(array('r2d1' => 'value1', 'r2d2' => 'value2'), $this->_frontendInstances['resource2']),
                ))
            );

        $advancedOptions = array(
            'resource1' => array('r1d1' => 'value1', 'r1d2' => 'value2'),
            'resource2' => array('r2d1' => 'value1', 'r2d2' => 'value2'),
        );

        $defaultOptions = array(
            'data1' => 'value1',
            'data2' => 'value2',
        );
        $this->_model = new \Magento\App\Cache\Frontend\Pool(
            $config, $frontendFactory, $defaultOptions, $advancedOptions);
    }

    /**
     * Test that constructor delays object initialization (does not perform any initialization of its own)
     */
    public function testConstructorNoInitialization()
    {
        $config = $this->getMock('Magento\App\Config', array(), array(), '', false);
        $frontendFactory = $this->getMock('Magento\App\Cache\Frontend\Factory', array(), array(), '', false);
        $frontendFactory
            ->expects($this->never())
            ->method('create')
        ;
        new \Magento\App\Cache\Frontend\Pool($config, $frontendFactory);
    }

    public function testCurrent()
    {
        $this->assertEquals(
            $this->_frontendInstances[\Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID],
            $this->_model->current()
        );
    }

    public function testKey()
    {
        $this->assertEquals(
            \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID,
            $this->_model->key()
        );
    }

    public function testNext()
    {
        $this->assertEquals(
            \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID,
            $this->_model->key()
        );

        $this->_model->next();
        $this->assertEquals(
            'resource1',
            $this->_model->key()
        );
        $this->assertSame(
            $this->_frontendInstances['resource1'],
            $this->_model->current()
        );

        $this->_model->next();
        $this->assertEquals(
            'resource2',
            $this->_model->key()
        );
        $this->assertSame(
            $this->_frontendInstances['resource2'],
            $this->_model->current()
        );

        $this->_model->next();
        $this->assertNull($this->_model->key());
        $this->assertFalse($this->_model->current());
    }

    public function testRewind()
    {
        $this->_model->next();
        $this->assertNotEquals(
            \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID,
            $this->_model->key()
        );

        $this->_model->rewind();
        $this->assertEquals(
            \Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID,
            $this->_model->key()
        );
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
        $this->assertSame($this->_frontendInstances[\Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID],
            $this->_model->get(\Magento\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID));
        $this->assertSame($this->_frontendInstances['resource1'], $this->_model->get('resource1'));
        $this->assertSame($this->_frontendInstances['resource2'], $this->_model->get('resource2'));
    }

}
