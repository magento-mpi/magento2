<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_StateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_Resource_Cache|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \Magento\Cache\FrontendInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheFrontend;

    /**
     * @param string $cacheType
     * @param array $typeOptions
     * @param bool $appInstalled
     * @param bool $banAll
     * @param bool $expectedIsEnabled
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($cacheType, $typeOptions, $appInstalled, $banAll, $expectedIsEnabled)
    {
        $model = $this->_buildModel($typeOptions, array(), $appInstalled, $banAll);
        $actualIsEnabled = $model->isEnabled($cacheType);
        $this->assertEquals($expectedIsEnabled, $actualIsEnabled);
    }

    /**
     * @return array
     */
    public static function isEnabledDataProvider()
    {
        return array(
            'enabled' => array(
                'cacheType' =>          'cache_type',
                'typeOptions' =>        array('some_type' => false, 'cache_type' => true),
                'appInstalled' =>       true,
                'banAll' =>             false,
                'expectedIsEnabled' =>  true,
            ),
            'disabled' => array(
                'cacheType' =>          'cache_type',
                'typeOptions' =>        array('some_type' => true, 'cache_type' => false),
                'appInstalled' =>       true,
                'banAll' =>             false,
                'expectedIsEnabled' =>  false,
            ),
            'unknown is disabled' => array(
                'cacheType' =>          'unknown_cache_type',
                'typeOptions' =>        array('some_type' => true),
                'appInstalled' =>       true,
                'banAll' =>             false,
                'expectedIsEnabled' =>  false,
            ),
            'disabled, when app is not installed' => array(
                'cacheType' =>          'cache_type',
                'typeOptions' =>        array('cache_type' => true),
                'appInstalled' =>       false,
                'banAll' =>             false,
                'expectedIsEnabled' =>  false,
            ),
            'disabled, when all caches are banned' => array(
                'cacheType' =>          'cache_type',
                'typeOptions' =>        array('cache_type' => true),
                'appInstalled' =>       true,
                'banAll' =>             true,
                'expectedIsEnabled' =>  false,
            ),
        );
    }

    /**
     * Builds model to be tested
     *
     * @param array|false $cacheTypeOptions
     * @param array|false $resourceTypeOptions
     * @param bool $appInstalled
     * @param bool $banAll
     * @return Magento_Core_Model_Cache_StateInterface
     */
    protected function _buildModel(
        $cacheTypeOptions,
        $resourceTypeOptions = false,
        $appInstalled = true,
        $banAll = false
    ) {
        $this->_cacheFrontend = $this->getMock('Magento\Cache\FrontendInterface');
        $this->_cacheFrontend->expects($this->any())
            ->method('load')
            ->with(Magento_Core_Model_Cache_State::CACHE_ID)
            ->will($this->returnValue(
            $cacheTypeOptions === false ? false : serialize($cacheTypeOptions)
        ));
        $cacheFrontendPool = $this->getMock('Magento_Core_Model_Cache_Frontend_Pool', array(), array(), '', false);
        $cacheFrontendPool->expects($this->any())
            ->method('get')
            ->with(Magento_Core_Model_Cache_Frontend_Pool::DEFAULT_FRONTEND_ID)
            ->will($this->returnValue($this->_cacheFrontend));

        $this->_resource = $this->getMock('Magento_Core_Model_Resource_Cache', array(), array(), '', false);
        $this->_resource->expects($this->any())
            ->method('getAllOptions')
            ->will($this->returnValue($resourceTypeOptions));

        $appState = $this->getMock('Magento_Core_Model_App_State');
        $appState->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue($appInstalled));

        $this->_model = new Magento_Core_Model_Cache_State($this->_resource, $cacheFrontendPool, $appState, $banAll);

        return $this->_model;
    }

    /**
     * The model must fetch data via its resource, if the cache type list is not cached
     * (e.g. cache load result is FALSE)
     */
    public function testIsEnabledFallbackToResource()
    {
        $model = $this->_buildModel(array(), array('cache_type' => true));
        $this->assertFalse($model->isEnabled('cache_type'));

        $model = $this->_buildModel(false, array('cache_type' => true));
        $this->assertTrue($model->isEnabled('cache_type'));
    }

    public function testSetEnabledIsEnabled()
    {
        $model = $this->_buildModel(array('cache_type' => false));
        $model->setEnabled('cache_type', true);
        $this->assertTrue($model->isEnabled('cache_type'));

        $model->setEnabled('cache_type', false);
        $this->assertFalse($model->isEnabled('cache_type'));
    }

    public function testPersist()
    {
        $cacheTypes = array('cache_type' => false);
        $model = $this->_buildModel($cacheTypes);

        $this->_resource->expects($this->once())
            ->method('saveAllOptions')
            ->with($cacheTypes);
        $this->_cacheFrontend->expects($this->once())
            ->method('remove')
            ->with(Magento_Core_Model_Cache_State::CACHE_ID);

        $model->persist();
    }
}
