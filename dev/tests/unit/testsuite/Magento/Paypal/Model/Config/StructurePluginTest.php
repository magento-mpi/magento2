<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\Config;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class StructurePluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Paypal\Model\Config\StructurePlugin */
    protected $_model;

    /** @var \Magento\Backend\Model\Config\ScopeDefiner|\PHPUnit_Framework_MockObject_MockObject */
    protected $_scopeDefiner;

    /** @var \Magento\Paypal\Helper\Backend|\PHPUnit_Framework_MockObject_MockObject */
    protected $_helper;

    protected function setUp()
    {
        $this->_scopeDefiner = $this->getMock('Magento\Backend\Model\Config\ScopeDefiner', [], [], '', false);
        $this->_helper = $this->getMock('Magento\Paypal\Helper\Backend', [], [], '', false);

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\Paypal\Model\Config\StructurePlugin',
            ['scopeDefiner' => $this->_scopeDefiner, 'helper' => $this->_helper]
        );
    }

    /**
     * @param array $pathParts
     * @param bool $returnResult
     * @dataProvider aroundGetElementByPathPartsNonPaymentDataProvider
     */
    public function testAroundGetElementByPathPartsNonPayment($pathParts, $returnResult) {
        $result = $returnResult
            ? $this->getMockForAbstractClass('Magento\Backend\Model\Config\Structure\ElementInterface')
            : null;
        $this->_aroundGetElementByPathPartsAssertResult(
            $result,
            $this->_getElementByPathPartsCallback($pathParts, $result),
            $pathParts
        );
    }

    public function aroundGetElementByPathPartsNonPaymentDataProvider()
    {
        return [
            [['non-payment', 'group1', 'group2', 'field'], true],
            [['non-payment'], true],
            [['non-payment', 'group1', 'group2', 'field'], false],
            [['non-payment'], false],
        ];
    }

    /**
     * @param array $pathParts
     * @param string $countryCode
     * @param array $expectedPathParts
     * @dataProvider aroundGetElementByPathPartsDataProvider
     */
    public function testAroundGetElementByPathPartsNoResult($pathParts, $countryCode, $expectedPathParts) {
        $this->_getElementByPathPartsPrepareHelper($countryCode);
        $this->_aroundGetElementByPathPartsAssertResult(
            null,
            $this->_getElementByPathPartsCallback($expectedPathParts, null),
            $pathParts
        );
    }

    /**
     * @param array $pathParts
     * @param string $countryCode
     * @param array $expectedPathParts
     * @dataProvider aroundGetElementByPathPartsDataProvider
     */
    public function testAroundGetElementByPathParts($pathParts, $countryCode, $expectedPathParts) {
        $this->_getElementByPathPartsPrepareHelper($countryCode);
        $result = $this->getMockForAbstractClass('Magento\Backend\Model\Config\Structure\ElementInterface');
        $self = $this;
        $getElementByPathParts = function ($pathParts) use ($self, $expectedPathParts, $result) {
            $self->assertEquals($expectedPathParts, $pathParts);
            $scope = 'any scope';
            $self->_scopeDefiner->expects($self->once())
                ->method('getScope')
                ->will($self->returnValue($scope));
            $result->expects($self->once())
                ->method('getData')
                ->will($self->returnValue([]));
            $result->expects($self->once())
                ->method('setData')
                ->with(['showInDefault' => true, 'showInWebsite' => true, 'showInStore' => true], $scope)
                ->will($self->returnSelf());
            return $result;
        };
        $this->_aroundGetElementByPathPartsAssertResult($result, $getElementByPathParts, $pathParts);
    }

    public function aroundGetElementByPathPartsDataProvider()
    {
        return [
            [
                ['payment', 'group1', 'group2', 'field'],
                'any',
                ['payment_other', 'group1', 'group2', 'field']
            ],
            [
                ['payment', 'group1', 'group2', 'field'],
                'DE',
                ['payment_de', 'group1', 'group2', 'field']
            ],
            [['payment'], 'GB', ['payment_gb']],
            [['payment'], 'any', ['payment_other']],
        ];
    }

    /**
     * Assert result of aroundGetElementByPathParts method
     *
     * @param \PHPUnit_Framework_MockObject_MockObject|null $result
     * @param \Closure $getElementByPathParts
     * @param array $pathParts
     */
    private function _aroundGetElementByPathPartsAssertResult($result, $getElementByPathParts, $pathParts)
    {
        $this->assertEquals($result, $this->_model->aroundGetElementByPathParts(
            $this->getMock('Magento\Backend\Model\Config\Structure', [], [], '', false),
            $getElementByPathParts,
            $pathParts
        ));
    }

    /**
     * Get callback for aroundGetElementByPathParts method
     *
     * @param array $expectedPathParts
     * @param \PHPUnit_Framework_MockObject_MockObject|null $result
     * @return \Closure
     */
    private function _getElementByPathPartsCallback($expectedPathParts, $result)
    {
        $self = $this;
        return function ($pathParts) use ($self, $expectedPathParts, $result) {
            $self->assertEquals($expectedPathParts, $pathParts);
            return $result;
        };
    }

    /**
     * Prepare helper for test
     *
     * @param string $countryCode
     */
    private function _getElementByPathPartsPrepareHelper($countryCode)
    {
        $this->_helper->expects($this->once())
            ->method('getConfigurationCountryCode')
            ->will($this->returnValue($countryCode));
    }
}
