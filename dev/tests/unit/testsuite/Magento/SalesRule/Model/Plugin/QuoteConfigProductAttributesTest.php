<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Model\Plugin;

class QuoteConfigProductAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Plugin\QuoteConfigProductAttributes|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $plugin;

    /**
     * @var \Magento\SalesRule\Model\Resource\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleResource;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->ruleResource = $this->getMock('Magento\SalesRule\Model\Resource\Rule', [], [], '', false);

        $this->plugin = $objectManager->getObject(
            'Magento\SalesRule\Model\Plugin\QuoteConfigProductAttributes',
            [
                'ruleResource' => $this->ruleResource
            ]
        );
    }

    public function testAfterGetProductAttributes()
    {
        $subject = $this->getMock('Magento\Sales\Model\Quote\Config', [], [], '', false);
        $attributeCode = 'code of the attribute';
        $expected = [0 => $attributeCode];

        $this->ruleResource->expects($this->once())
            ->method('getActiveAttributes')
            ->will(
                $this->returnValue(
                    [
                        ['attribute_code' => $attributeCode, 'enabled' => true],
                    ]
                )
            );

        $this->assertEquals($expected, $this->plugin->afterGetProductAttributes($subject, []));
    }
}
