<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Solr\Model\Layer\Search\AvailabilityFlag;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineProviderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \Magento\Solr\Model\Layer\Search\AvailabilityFlag\Plugin
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->filterMock = $this->getMock(
            'Magento\Catalog\Model\Layer\Filter\AbstractFilter', [], [], '', false
        );
        $this->filters = [$this->filterMock];

        $this->subjectMock = $this->getMock('Magento\Catalog\Model\Layer\Search\AvailabilityFlag');
        $this->helperMock = $this->getMock('Magento\Solr\Helper\Data', [], [], '', false);
        $this->layerMock = $this->getMock('Magento\Catalog\Model\Layer', [], [], '', false);
        $this->stateMock = $this->getMock('Magento\Catalog\Model\Layer\State', [], [], '', false);
        $this->model = new Plugin($this->helperMock);
    }

    /**
     * @param bool $isThirdPart
     * @param bool $isActive
     *
     * @dataProvider aroundIsEnabledWithThirdPartEngineOffDataProvider
     * @covers       \Magento\Solr\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     * @covers       \Magento\Solr\Model\Layer\Search\AvailabilityFlag\Plugin::__construct
     */
    public function testIsEnabledWithThirdPartEngineOff($isThirdPart, $isActive)
    {
        $this->helperMock->expects($this->once())->method('isThirdPartSearchEngine')
            ->will($this->returnValue($isThirdPart));
        $this->helperMock->expects($this->any())->method('isActiveEngine')->will($this->returnValue($isActive));

        /**
         * @param \Magento\Catalog\Model\Layer $layer
         * @param array $filters
         * @return bool
         */
        $proceed = function ($layer, $filters) {
            $this->assertEquals($this->layerMock, $layer);
            $this->assertEquals([], $filters);

            return false;
        };
        $this->assertFalse($this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, []));
    }

    /**
     * @return array
     */
    public function aroundIsEnabledWithThirdPartEngineOffDataProvider()
    {
        return [
            [false, false],
            [true, false],
            [false, true],
        ];
    }

    /**
     * @param int $itemsCount
     * @param array $filters
     * @param bool $expectedResult
     *
     * @dataProvider aroundIsEnabledDataProvider
     * @covers \Magento\Solr\Model\Layer\Search\AvailabilityFlag\Plugin::aroundIsEnabled
     * @covers \Magento\Solr\Model\Layer\Search\AvailabilityFlag\Plugin::canShowOptions
     */
    public function testAroundIsEnabled($itemsCount, $filters, $expectedResult)
    {
        $this->helperMock->expects($this->once())->method('isThirdPartSearchEngine')->will($this->returnValue(true));
        $this->helperMock->expects($this->once())->method('isActiveEngine')->will($this->returnValue(true));

        $this->layerMock->expects($this->any())->method('getState')->will($this->returnValue($this->stateMock));
        $this->stateMock->expects($this->any())->method('getFilters')->will($this->returnValue($filters));
        $this->filterMock->expects($this->once())->method('getItemsCount')->will($this->returnValue($itemsCount));

        $proceed = function () {
            $this->fail('Subject should not be called in this scenario');
        };

        $this->assertEquals(
            $expectedResult,
            $this->model->aroundIsEnabled($this->subjectMock, $proceed, $this->layerMock, $this->filters)
        );
    }

    /**
     * @return array
     */
    public function aroundIsEnabledDataProvider()
    {
        return [
            [
                'itemsCount' => 0,
                'filters' => [],
                'expectedResult' => false,
            ],
            [
                'itemsCount' => 0,
                'filters' => ['filter'],
                'expectedResult' => true,
            ],
            [
                'itemsCount' => 1,
                'filters' => 0,
                'expectedResult' => true,
            ],
            [
                'itemsCount' => 1,
                'filters' => ['filter'],
                'expectedResult' => true,
            ]
        ];
    }
}
