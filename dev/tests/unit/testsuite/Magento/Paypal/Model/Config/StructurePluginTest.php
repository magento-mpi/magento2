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
     * @param string $countryCode
     * @param array $expectedPathParts
     * @param bool $returnResult
     * @dataProvider aroundGetElementByPathPartsDataProvider
     */
    public function testAroundGetElementByPathParts(
        $pathParts,
        $countryCode,
        $expectedPathParts,
        $returnResult
    ) {
        $sectionChanged = $pathParts[0] != $expectedPathParts[0];
        if ($sectionChanged) {
            $this->_helper->expects($this->once())
                ->method('getConfigurationCountryCode')
                ->will($this->returnValue($countryCode));
        } else {
            $this->_helper->expects($this->never())
                ->method('getConfigurationCountryCode');
        }
        $result = $returnResult
            ? $this->getMockForAbstractClass('Magento\Backend\Model\Config\Structure\ElementInterface')
            : null;
        $self = $this;
        $getElementByPathParts = function ($pathParts) use (
            $self,
            $expectedPathParts,
            $result,
            $sectionChanged
        ) {
            $self->assertEquals($expectedPathParts, $pathParts);
            $scope = 'any scope';
            if ($sectionChanged && isset($result)) {
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
            } else {
                $self->_scopeDefiner->expects($self->never())
                    ->method('getScope');
            }
            return $result;
        };
        $this->assertEquals($result, $this->_model->aroundGetElementByPathParts(
            $this->getMock('Magento\Backend\Model\Config\Structure', [], [], '', false),
            $getElementByPathParts,
            $pathParts
        ));
    }

    public function aroundGetElementByPathPartsDataProvider()
    {
        $data = [];
        foreach ([true, false] as $returnResult) {
            $data = array_merge(
                $data,
                [
                    [
                        ['payment', 'group1', 'group2', 'field'],
                        'any',
                        ['payment_other', 'group1', 'group2', 'field'],
                        $returnResult
                    ],
                    [
                        ['payment', 'group1', 'group2', 'field'],
                        'DE',
                        ['payment_de', 'group1', 'group2', 'field'],
                        $returnResult
                    ],
                    [['payment'], 'GB', ['payment_gb'], $returnResult],
                    [
                        ['non-payment', 'group1', 'group2', 'field'],
                        'any',
                        ['non-payment', 'group1', 'group2', 'field'],
                        $returnResult
                    ],
                    [['non-payment'], 'any', ['non-payment'], $returnResult],
                ]
            );
        }
        return $data;
    }
}
