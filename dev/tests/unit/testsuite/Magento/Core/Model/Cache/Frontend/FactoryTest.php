<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_Frontend_FactoryTest extends PHPUnit_Framework_TestCase
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
            'Magento_Cache_FrontendInterface',
            $result,
            'Created object must implement Magento_Cache_FrontendInterface'
        );
        $this->assertInstanceOf(
            'Magento_Cache_Core',
            $result->getLowLevelFrontend(),
            'Created object must have Magento_Cache_Core frontend by default'
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
                'frontend_options' => array(
                    'lifetime' => 2601
                ),
                'backend_options' => array(
                    'file_extension' => '.wtf'
                ),
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
            'default id prefix' => array(
                array(
                    'backend' => 'Zend_Cache_Backend_BlackHole',
                ),
                'a3c_', // start of md5('CONFIG_DIR')
            ),
            'id prefix in "id_prefix" option' => array(
                array(
                    'backend' => 'Zend_Cache_Backend_BlackHole',
                    'id_prefix' => 'id_prefix_value'
                ),
                'id_prefix_value',
            ),
            'id prefix in "prefix" option' => array(
                array(
                    'backend' => 'Zend_Cache_Backend_BlackHole',
                    'prefix' => 'prefix_value'
                ),
                'prefix_value',
            ),
        );
    }

    public function testCreateDecorators()
    {
        $model = $this->_buildModelForCreate(
            array(),
            array(
                array(
                    'class' => 'Magento_Core_Model_Cache_Frontend_FactoryTest_CacheDecoratorDummy',
                    'parameters' => array('param' => 'value')
                )
            )
        );
        $result = $model->create(array('backend' => 'Zend_Cache_Backend_BlackHole'));

        $this->assertInstanceOf('Magento_Core_Model_Cache_Frontend_FactoryTest_CacheDecoratorDummy', $result);

        $params = $result->getParams();
        $this->assertArrayHasKey('param', $params);
        $this->assertEquals($params['param'], 'value');
    }

    /**
     * Create the model to be tested, providing it with all required dependencies
     *
     * @param array $enforcedOptions
     * @param array $decorators
     * @return Magento_Core_Model_Cache_Frontend_Factory
     */
    protected function _buildModelForCreate($enforcedOptions = array(), $decorators = array())
    {
        $processFrontendFunc = function ($class, $params) {
            switch ($class) {
                case 'Magento_Cache_Frontend_Adapter_Zend':
                    return new $class($params['frontend']);
                case 'Magento_Core_Model_Cache_Frontend_FactoryTest_CacheDecoratorDummy':
                    $frontend = $params['frontend'];
                    unset($params['frontend']);
                    return new $class($frontend, $params);
                default:
                    throw new Exception("Test is not designed to create {$class} objects");
                    break;
            }
        };
        /** @var $objectManager PHPUnit_Framework_MockObject_MockObject */
        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);
        $objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnCallback($processFrontendFunc));

        $filesystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $filesystem->expects($this->any())
            ->method('isDirectory')
            ->will($this->returnValue(true));
        $filesystem->expects($this->any())
            ->method('isWritable')
            ->will($this->returnValue(true));

        $map = array(
            array(Magento_Core_Model_Dir::CACHE, 'CACHE_DIR'),
            array(Magento_Core_Model_Dir::CONFIG, 'CONFIG_DIR'),
        );
        $dirs = $this->getMock('Magento_Core_Model_Dir', array('getDir'), array(), '', false);
        $dirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValueMap($map));

        $model = new Magento_Core_Model_Cache_Frontend_Factory($objectManager, $filesystem, $dirs, $enforcedOptions,
            $decorators);

        return $model;
    }
}
