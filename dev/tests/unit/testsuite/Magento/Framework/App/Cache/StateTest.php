<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\App\Cache;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Cache\State\Options|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Cache\FrontendInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheFrontend;

    /**
     * @param string $cacheType
     * @param array $typeOptions
     * @param bool $banAll
     * @param bool $expectedIsEnabled
     * @dataProvider isEnabledDataProvider
     */
    public function testIsEnabled($cacheType, $typeOptions, $banAll, $expectedIsEnabled)
    {
        $model = $this->_buildModel($typeOptions, [], $banAll);
        $actualIsEnabled = $model->isEnabled($cacheType);
        $this->assertEquals($expectedIsEnabled, $actualIsEnabled);
    }

    /**
     * @return array
     */
    public static function isEnabledDataProvider()
    {
        return [
            'enabled' => [
                'cacheType' => 'cache_type',
                'typeOptions' => ['some_type' => false, 'cache_type' => true],
                'banAll' => false,
                'expectedIsEnabled' => true,
            ],
            'disabled' => [
                'cacheType' => 'cache_type',
                'typeOptions' => ['some_type' => true, 'cache_type' => false],
                'banAll' => false,
                'expectedIsEnabled' => false,
            ],
            'unknown is disabled' => [
                'cacheType' => 'unknown_cache_type',
                'typeOptions' => ['some_type' => true],
                'banAll' => false,
                'expectedIsEnabled' => false,
            ],
            'disabled, when all caches are banned' => [
                'cacheType' => 'cache_type',
                'typeOptions' => ['cache_type' => true],
                'banAll' => true,
                'expectedIsEnabled' => false,
            ]
        ];
    }

    /**
     * Builds model to be tested
     *
     * @param array|false $cacheTypeOptions
     * @param array|false $resourceTypeOptions
     * @param bool $banAll
     * @return \Magento\Framework\App\Cache\StateInterface
     */
    protected function _buildModel(
        $cacheTypeOptions,
        $resourceTypeOptions = false,
        $banAll = false
    ) {
        $this->_cacheFrontend = $this->getMock('Magento\Framework\Cache\FrontendInterface');
        $this->_cacheFrontend->expects(
            $this->any()
        )->method(
            'load'
        )->with(
            \Magento\Framework\App\Cache\State::CACHE_ID
        )->will(
            $this->returnValue($cacheTypeOptions === false ? false : serialize($cacheTypeOptions))
        );
        $cacheFrontendPool = $this->getMock('Magento\Framework\App\Cache\Frontend\Pool', [], [], '', false);
        $cacheFrontendPool->expects(
            $this->any()
        )->method(
            'get'
        )->with(
            \Magento\Framework\App\Cache\Frontend\Pool::DEFAULT_FRONTEND_ID
        )->will(
            $this->returnValue($this->_cacheFrontend)
        );

        $this->_resource = $this->getMock('Magento\Framework\App\Cache\State\Options', [], [], '', false);
        $this->_resource->expects(
            $this->any()
        )->method(
            'getAllOptions'
        )->will(
            $this->returnValue($resourceTypeOptions)
        );

        $this->_model = new \Magento\Framework\App\Cache\State(
            $this->_resource,
            $cacheFrontendPool,
            $banAll
        );

        return $this->_model;
    }

    /**
     * The model must fetch data via its resource, if the cache type list is not cached
     * (e.g. cache load result is FALSE)
     */
    public function testIsEnabledFallbackToResource()
    {
        $model = $this->_buildModel([], ['cache_type' => true]);
        $this->assertFalse($model->isEnabled('cache_type'));

        $model = $this->_buildModel(false, ['cache_type' => true]);
        $this->assertTrue($model->isEnabled('cache_type'));
    }

    public function testSetEnabledIsEnabled()
    {
        $model = $this->_buildModel(['cache_type' => false]);
        $model->setEnabled('cache_type', true);
        $this->assertTrue($model->isEnabled('cache_type'));

        $model->setEnabled('cache_type', false);
        $this->assertFalse($model->isEnabled('cache_type'));
    }

    public function testPersist()
    {
        $cacheTypes = ['cache_type' => false];
        $model = $this->_buildModel($cacheTypes);

        $this->_resource->expects($this->once())->method('saveAllOptions')->with($cacheTypes);
        $this->_cacheFrontend->expects($this->once())
            ->method('remove')
            ->with(\Magento\Framework\App\Cache\State::CACHE_ID);

        $model->persist();
    }
}
