<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Cache\Frontend;

use Magento\Framework\App\Filesystem\DirectoryList;

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
            'Magento\Framework\Cache\FrontendInterface',
            $result,
            'Created object must implement \Magento\Framework\Cache\FrontendInterface'
        );
        $this->assertInstanceOf(
            'Magento\Framework\Cache\Core',
            $result->getLowLevelFrontend(),
            'Created object must have \Magento\Framework\Cache\Core frontend by default'
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
                    'class' => 'Magento\Framework\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy',
                    'parameters' => array('param' => 'value')
                )
            )
        );
        $result = $model->create(array('backend' => 'Zend_Cache_Backend_BlackHole'));

        $this->assertInstanceOf('Magento\Framework\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy', $result);

        $params = $result->getParams();
        $this->assertArrayHasKey('param', $params);
        $this->assertEquals($params['param'], 'value');
    }

    /**
     * Create the model to be tested, providing it with all required dependencies
     *
     * @param array $enforcedOptions
     * @param array $decorators
     * @return \Magento\Framework\App\Cache\Frontend\Factory
     */
    protected function _buildModelForCreate($enforcedOptions = array(), $decorators = array())
    {
        $processFrontendFunc = function ($class, $params) {
            switch ($class) {
                case 'Magento\Framework\Cache\Frontend\Adapter\Zend':
                    return new $class($params['frontend']);
                case 'Magento\Framework\App\Cache\Frontend\FactoryTest\CacheDecoratorDummy':
                    $frontend = $params['frontend'];
                    unset($params['frontend']);
                    return new $class($frontend, $params);
                default:
                    throw new \Exception("Test is not designed to create {$class} objects");
                    break;
            }
        };
        /** @var $objectManager \PHPUnit_Framework_MockObject_MockObject */
        $objectManager = $this->getMock('Magento\Framework\ObjectManager', array(), array(), '', false);
        $objectManager->expects($this->any())->method('create')->will($this->returnCallback($processFrontendFunc));

        $map = array(
            array(DirectoryList::CACHE, 'CACHE_DIR'),
            array(DirectoryList::CONFIG, 'CONFIG_DIR')
        );

        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', array('getPath'), array(), '', false);

        $filesystem->expects($this->any())->method('getPath')->will($this->returnValueMap($map));

        $resource = $this->getMock('Magento\Framework\App\Resource', array(), array(), '', false);

        $model = new \Magento\Framework\App\Cache\Frontend\Factory(
            $objectManager,
            $filesystem,
            $resource,
            $enforcedOptions,
            $decorators
        );

        return $model;
    }
}
