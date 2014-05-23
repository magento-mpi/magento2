<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Model\Checks;

class SpecificationFactoryTest extends \PHPUnit_Framework_TestCase
{
    const SPECIFICATION_KEY = 'specification';

    /**
     * @var \Magento\Payment\Model\Checks\CompositeFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_compositeFactory;

    public function setUp()
    {
        $this->_compositeFactory = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\CompositeFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
    }

    public function testCreate()
    {
        $specificationMapping = $this->_getPreparedSpecificationListMapping();
        $expectedComposite = $this->_getComposite();
        $modelFactory = new SpecificationFactory($this->_compositeFactory, $specificationMapping);
        $this->_compositeFactory->expects($this->once())->method('create')->with(
            ['list' => $specificationMapping]
        )->will($this->returnValue($expectedComposite));

        $this->assertEquals($expectedComposite, $modelFactory->create([self::SPECIFICATION_KEY]));
    }

    /**
     * Returns list of SpecificationInterface, [key => SpecificationInterface]
     * @return SpecificationInterface[]
     */
    private function _getPreparedSpecificationListMapping()
    {
        $specification = $this->getMockBuilder(
            'Magento\Payment\Model\Checks\SpecificationInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        return [self::SPECIFICATION_KEY => $specification];
    }

    /**
     * Returns prepared composite mock
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getComposite()
    {
        return $this->getMockBuilder(
            'Magento\Payment\Model\Checks\Composite'
        )->disableOriginalConstructor()->setMethods([])->getMock();
    }
}
