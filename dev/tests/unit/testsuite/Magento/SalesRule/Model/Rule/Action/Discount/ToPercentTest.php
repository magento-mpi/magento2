<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\SalesRule\Model\Rule\Action\Discount;

class ToPercentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Action\Discount\ToPercent
     */
    protected $model;

    /**
     * @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $validator;

    /**
     * @var \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $discountDataFactory;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->validator = $this->getMockBuilder(
            'Magento\SalesRule\Model\Validator'
        )->disableOriginalConstructor()->setMethods(
            ['getItemPrice', 'getItemBasePrice', 'getItemOriginalPrice', 'getItemBaseOriginalPrice', '__wakeup']
        )->getMock();

        $this->discountDataFactory = $this->getMockBuilder(
            'Magento\SalesRule\Model\Rule\Action\Discount\DataFactory'
        )->disableOriginalConstructor()->setMethods(
            ['create']
        )->getMock();

        $this->model = $helper->getObject(
            'Magento\SalesRule\Model\Rule\Action\Discount\ToPercent',
            ['discountDataFactory' => $this->discountDataFactory, 'validator' => $this->validator]
        );
    }

    /**
     * @param $qty
     * @param $ruleData
     * @param $itemData
     * @param $validItemData
     * @param $expectedRuleDiscountQty
     * @param $expectedDiscountData
     * @dataProvider calculateDataProvider
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testCalculate(
        $qty,
        $ruleData,
        $itemData,
        $validItemData,
        $expectedRuleDiscountQty,
        $expectedDiscountData
    ) {
        $discountData = $this->getMockBuilder(
            'Magento\SalesRule\Model\Rule\Action\Discount\Data'
        )->disableOriginalConstructor()->setMethods(
            ['setAmount', 'setBaseAmount', 'setOriginalAmount', 'setBaseOriginalAmount']
        )->getMock();

        $this->discountDataFactory->expects($this->once())->method('create')->will($this->returnValue($discountData));

        $rule = $this->getMockBuilder(
            'Magento\SalesRule\Model\Rule'
        )->disableOriginalConstructor()->setMethods(
            ['getDiscountAmount', 'getDiscountQty', '__wakeup']
        )->getMock();

        $item = $this->getMockBuilder(
            'Magento\Sales\Model\Quote\Item\AbstractItem'
        )->disableOriginalConstructor()->setMethods(
            [
                'getDiscountAmount',
                'getBaseDiscountAmount',
                'getDiscountPercent',
                'setDiscountPercent',
                '__wakeup',
                'getQuote',
                'getAddress',
                'getOptionByCode',
            ]
        )->getMock();

        $this->validator->expects(
            $this->atLeastOnce()
        )->method(
            'getItemPrice'
        )->with(
            $item
        )->will(
            $this->returnValue($validItemData['price'])
        );
        $this->validator->expects(
            $this->atLeastOnce()
        )->method(
            'getItemBasePrice'
        )->with(
            $item
        )->will(
            $this->returnValue($validItemData['basePrice'])
        );
        $this->validator->expects(
            $this->atLeastOnce()
        )->method(
            'getItemOriginalPrice'
        )->with(
            $item
        )->will(
            $this->returnValue($validItemData['originalPrice'])
        );
        $this->validator->expects(
            $this->atLeastOnce()
        )->method(
            'getItemBaseOriginalPrice'
        )->with(
            $item
        )->will(
            $this->returnValue($validItemData['baseOriginalPrice'])
        );

        $rule->expects(
            $this->atLeastOnce()
        )->method(
            'getDiscountAmount'
        )->will(
            $this->returnValue($ruleData['discountAmount'])
        );
        $rule->expects(
            $this->atLeastOnce()
        )->method(
            'getDiscountQty'
        )->will(
            $this->returnValue($ruleData['discountQty'])
        );

        $item->expects(
            $this->atLeastOnce()
        )->method(
            'getDiscountAmount'
        )->will(
            $this->returnValue($itemData['discountAmount'])
        );
        $item->expects(
            $this->atLeastOnce()
        )->method(
            'getBaseDiscountAmount'
        )->will(
            $this->returnValue($itemData['baseDiscountAmount'])
        );
        if (!$ruleData['discountQty'] || $ruleData['discountQty'] > $qty) {
            $item->expects(
                $this->atLeastOnce()
            )->method(
                'getDiscountPercent'
            )->will(
                $this->returnValue($itemData['discountPercent'])
            );
            $item->expects($this->atLeastOnce())->method('setDiscountPercent')->with($expectedRuleDiscountQty);
        }

        $discountData->expects($this->once())->method('setAmount')->with($expectedDiscountData['amount']);
        $discountData->expects($this->once())->method('setBaseAmount')->with($expectedDiscountData['baseAmount']);
        $discountData->expects(
            $this->once()
        )->method(
            'setOriginalAmount'
        )->with(
            $expectedDiscountData['originalAmount']
        );
        $discountData->expects(
            $this->once()
        )->method(
            'setBaseOriginalAmount'
        )->with(
            $expectedDiscountData['baseOriginalAmount']
        );

        $this->assertEquals($discountData, $this->model->calculate($rule, $item, $qty));
    }

    /**
     * @return array
     */
    public function calculateDataProvider()
    {
        return [
            [
                'qty' => 3,
                'ruleData' => ['discountAmount' => 30, 'discountQty' => 5],
                'itemData' => ['discountAmount' => 10, 'baseDiscountAmount' => 50, 'discountPercent' => 55],
                'validItemData' => [
                    'price' => 50,
                    'basePrice' => 45,
                    'originalPrice' => 60,
                    'baseOriginalPrice' => 55,
                ],
                'expectedRuleDiscountQty' => 100,
                'expectedDiscountData' => [
                    'amount' => 98,
                    'baseAmount' => 59.5,
                    'originalAmount' => 119,
                    'baseOriginalAmount' => 108.5,
                ],
            ]
        ];
    }
}
