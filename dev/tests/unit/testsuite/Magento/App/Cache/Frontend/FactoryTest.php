<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Cache\Frontend;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/FactoryTest/CacheDecoratorDummy.php';
    }

    public function testCreate()
    {
        $model = $this->_buildModelForCreate();
        $result = $model->create(array('backend' => 'Zend_Cache_Backend_BlackHole'));

        $this->assertInstanceOf(
            'Magento\Cache\FrontendInterface',
            $result,
            'Created object must implement \Magento\Cache\FrontendInterface'
        );
        $this->assertInstanceOf(
            'Magento\Cache\Core',
            $result->getLowLevelFrontend(),
            'Created object must have \Magento\Cache\Core frontend by default'
        );
        $this->assertInstanceOf(
            'Zend_Cache_Backend_BlackHole',
            $result->getBackend(),
            'Created object must have backend as configured in backend options'
        );
    }

    public function testCreateOptions()
    {
        $model = $this->_buildModelForCreate();
        $result = $model->create(
            array(
                'backend' => 'Zend_Cache_Backend_Static',
                'frontend_options' => array('lifetime' => 2601),
                'backend_options' => array('file_extension' => '.wtf')
            )
        );

        $frontend = $result->getLowLevelFrontend();
        $backend = $result->getBackend();

        $this->assertEquals(2601, $frontend->getOption('lifetime'));
        $this->assertEquals('.wtf', $backend->getOption('file_extension'));
    }

    public function testCreateEnforcedOptions()
    {
        $model = $this->_buildModelForCreate(array('backend' => 'Zend_Cache_Backend_Static'));
        $result = $model->create(array('backend' => 'Zend_Cache_Backend_BlackHole'));

        $this->assertInstanceOf('Zend_Cache_Backend_Static', $result->getBackend());
    }

    /**
     * @param array $options
     * @param string $expectedPrefix
     * @dataProvider idPrefixDataProvider
     */
    public function testIdPrefix($options, $expectedPrefix)
    {
        $model = $this->_buildModelForCreate(array('backend' => 'Zend_Cache_Backend_Static'));
        $result = $model->create($options);

        $frontend = $result->getLowLevelFrontend();
        $this->assertEquals($expectedPrefix, $frontend->getOption('cache_id_prefix'));
    }

    /**
     * @return array
     */
    public static function idPrefixDataProvider()
    {
        return array(
            // start of md5('CONFIG_DIR')
            'default id prefix' => array(array('backend' => 'Zend_Cache_Backend_BlackHole'), 'a3c_'),
            'id prefix in "id_prefix" option' => array(
                array('backend' => 'Zend_Cache_Backend_BlackHole', 'id_prefix' => 'id_prefix_value'),
                'id_prefix_value'
            ),
            'id prefix in "prefix" option' => array(
                array('backend' => 'Zend_Cache_Backend_BlackHole', 'prefix' => 'prefix_value'),
                'prefix_value'
            )
        );
    }

    public function testCreateDecorators()
    {
        $model = $this->_buildModelForCreate(
            array(),
            array(
                array(
                    'class' => 'Magento\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy',
                    'parameters' => array('param' => 'value')
                )
            )
        );
        $result = $model->create(array('backend' => 'Zend_Cache_Backend_BlackHole'));

        $this->assertInstanceOf('Magento\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy', $result);

        $params = $result->getParams();
        $this->assertArrayHasKey('param', $params);
        $this->assertEquals($params['param'], 'value');
    }

    /**
     * Create the model to be tested, providing it with all required dependencies
     *
     * @param array $enforcedOptions
     * @param array $decorators
     * @return \Magento\App\Cache\Frontend\Factory
     */
    protected function _buildModelForCreate($enforcedOptions = array(), $decorators = array())
    {
        $processFrontendFunc = function ($class, $params) {
            switch ($class) {
                case 'Magento\Cache\Frontend\Adapter\Zend':
                    return new $class($params['frontend']);
                case 'Magento\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy':
                    $frontend = $params['frontend'];
                    unset($params['frontend']);
                    return new $class($frontend, $params);
                default:
                    throw new \Exception("Test is not designed to create {$class} objects");
                    break;
            }
        };
        /** @var $objectManager \PHPUnit_Framework_MockObject_MockObject */
        $objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);
        $objectManager->expects($this->any())->method('create')->will($this->returnCallback($processFrontendFunc));

        $map = array(
            array(\Magento\App\Filesystem::CACHE_DIR, 'CACHE_DIR'),
            array(\Magento\App\Filesystem::CONFIG_DIR, 'CONFIG_DIR')
        );

        $filesystem = $this->getMock('Magento\App\Filesystem', array('getPath'), array(), '', false);

        $filesystem->expects($this->any())->method('getPath')->will($this->returnValueMap($map));

        $resource = $this->getMock('Magento\App\Resource', array(), array(), '', false);

        $model = new \Magento\App\Cache\Frontend\Factory(
            $objectManager,
            $filesystem,
            $resource,
            $enforcedOptions,
            $decorators
        );

        return $model;
    }
}
