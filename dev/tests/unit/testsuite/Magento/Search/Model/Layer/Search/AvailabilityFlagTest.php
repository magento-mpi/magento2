<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

class AvailabilityFlagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $stateMock;

    /**
     * @var \Magento\Search\Model\Layer\Search\AvailabilityFlag
     */
    protected $model;

    protected function setUp()
    {
        $this->filterMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Filter\AbstractFilter', array(), array(), '', false
        );
        $this->filters = array($this->filterMock);
        $this->layerMock = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->stateMock = $this->getMock('\Magento\Catalog\Model\Layer\State', array(), array(), '', false);
    }

    /**
     * @dataProvider isEnabledDataProvider
     * @covers \Magento\Search\Model\Layer\Search\AvailabilityFlag::isEnabled
     */
    public function testIsEnabled()
    {
    }

    /**
     * @return array
     */
    public function isEnabledDataProvider()
    {
        return array(
        );
    }
}
