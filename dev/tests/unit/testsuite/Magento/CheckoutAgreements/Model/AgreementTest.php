<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Model;

class AgreementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CheckoutAgreements\Model\Agreement
     */
    protected $model;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject('\Magento\CheckoutAgreements\Model\Agreement');
    }

    /**
     * @covers \Magento\CheckoutAgreements\Model\Agreement::validateData
     *
     * @dataProvider validateDataDataProvider
     * @param \Magento\Framework\Object $inputData
     * @param array|bool $expectedResult
     */
    public function testValidateData($inputData, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->validateData($inputData));
    }

    /**
     * @return array
     */
    public function validateDataDataProvider()
    {
        return [
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('1px'),
                'expectedResult' => true
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('1.1px'),
                'expectedResult' => true
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('0.1in'),
                'expectedResult' => true
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('5%'),
                'expectedResult' => true
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('5'),
                'expectedResult' => [
                    "Please enter correct value for 'Content Height' field with units [px,pc,pt,ex,em,mm,cm,in,%]."
                ]
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('px'),
                'expectedResult' => [
                    "Please enter correct value for 'Content Height' field with units [px,pc,pt,ex,em,mm,cm,in,%]."
                ]
            ],
            [
                'inputData' => (new \Magento\Framework\Object())->setContentHeight('abracadabra'),
                'expectedResult' => [
                    "Please enter correct value for 'Content Height' field with units [px,pc,pt,ex,em,mm,cm,in,%]."
                ]
            ],
        ];
    }
}
