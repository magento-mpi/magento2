<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Frontend\Quote\Address;

class VatValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Frontend\Quote\Address\VatValidator
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var array
     */
    protected $testData;

    /**
     * @var \Magento\Object
     */
    protected $validationResult;

    protected function setUp()
    {
        $this->customerAddressMock = $this->getMock('Magento\Customer\Helper\Address', array(), array(), '', false);
        $this->customerDataMock = $this->getMock('Magento\Customer\Helper\Data', array(), array(), '', false);
        $this->customerDataMock->expects(
            $this->any()
        )->method(
            'getMerchantCountryCode'
        )->will(
            $this->returnValue('merchantCountryCode')
        );
        $this->customerDataMock->expects(
            $this->any()
        )->method(
            'getMerchantVatNumber'
        )->will(
            $this->returnValue('merchantVatNumber')
        );

        $this->storeMock = $this->getMock('Magento\Core\Model\Store', array(), array(), '', false);

        $this->quoteAddressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            array(
                'getCountryId',
                'getVatId',
                'getValidatedCountryCode',
                'getValidatedVatNumber',
                'getVatIsValid',
                'getVatRequestId',
                'getVatRequestDate',
                'getVatRequestSuccess',
                'getAddressType',
                'save',
                '__wakeup'
            ),
            array(),
            '',
            false,
            false
        );

        $this->testData = array(
            'is_valid' => true,
            'request_identifier' => 'test_request_identifier',
            'request_date' => 'test_request_date',
            'request_success' => true
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getVatIsValid'
        )->will(
            $this->returnValue($this->testData['is_valid'])
        );
        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getVatRequestId'
        )->will(
            $this->returnValue($this->testData['request_identifier'])
        );
        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getVatRequestDate'
        )->will(
            $this->returnValue($this->testData['request_date'])
        );
        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getVatRequestSuccess'
        )->will(
            $this->returnValue($this->testData['request_success'])
        );
        $this->quoteAddressMock->expects($this->any())->method('getCountryId')->will($this->returnValue('en'));
        $this->quoteAddressMock->expects($this->any())->method('getVatId')->will($this->returnValue('testVatID'));

        $this->validationResult = new \Magento\Object($this->testData);

        $this->model = new \Magento\Sales\Model\Observer\Frontend\Quote\Address\VatValidator(
            $this->customerAddressMock,
            $this->customerDataMock
        );
    }

    public function testValidateWithDisabledValidationOnEachTransaction()
    {
        $this->customerDataMock->expects($this->never())->method('checkVatNumber');

        $this->customerAddressMock->expects(
            $this->once()
        )->method(
            'hasValidateOnEachTransaction'
        )->with(
            $this->storeMock
        )->will(
            $this->returnValue(false)
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedCountryCode'
        )->will(
            $this->returnValue('en')
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedVatNumber'
        )->will(
            $this->returnValue('testVatID')
        );

        $this->quoteAddressMock->expects($this->never())->method('save');

        $this->assertEquals(
            $this->validationResult,
            $this->model->validate($this->quoteAddressMock, $this->storeMock)
        );
    }

    public function testValidateWithEnabledValidationOnEachTransaction()
    {
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'checkVatNumber'
        )->with(
            'en',
            'testVatID',
            'merchantCountryCode',
            'merchantVatNumber'
        )->will(
            $this->returnValue($this->validationResult)
        );

        $this->customerAddressMock->expects(
            $this->once()
        )->method(
            'hasValidateOnEachTransaction'
        )->with(
            $this->storeMock
        )->will(
            $this->returnValue(true)
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedCountryCode'
        )->will(
            $this->returnValue('en')
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedVatNumber'
        )->will(
            $this->returnValue('testVatID')
        );

        $this->quoteAddressMock->expects($this->once())->method('save');

        $this->assertEquals(
            $this->validationResult,
            $this->model->validate($this->quoteAddressMock, $this->storeMock)
        );
    }

    public function testValidateWithDifferentCountryIdAndValidatedCountryCode()
    {
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'checkVatNumber'
        )->with(
            'en',
            'testVatID',
            'merchantCountryCode',
            'merchantVatNumber'
        )->will(
            $this->returnValue($this->validationResult)
        );

        $this->customerAddressMock->expects(
            $this->once()
        )->method(
            'hasValidateOnEachTransaction'
        )->with(
            $this->storeMock
        )->will(
            $this->returnValue(false)
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedCountryCode'
        )->will(
            $this->returnValue('someCountryCode')
        );

        $this->quoteAddressMock->expects($this->any())->method('getVatId')->will($this->returnValue('testVatID'));

        $this->quoteAddressMock->expects($this->once())->method('save');

        $this->assertEquals(
            $this->validationResult,
            $this->model->validate($this->quoteAddressMock, $this->storeMock)
        );
    }

    public function testValidateWithDifferentVatNumberAndValidatedVatNumber()
    {
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'checkVatNumber'
        )->with(
            'en',
            'testVatID',
            'merchantCountryCode',
            'merchantVatNumber'
        )->will(
            $this->returnValue($this->validationResult)
        );

        $this->customerAddressMock->expects(
            $this->once()
        )->method(
            'hasValidateOnEachTransaction'
        )->with(
            $this->storeMock
        )->will(
            $this->returnValue(false)
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getValidatedCountryCode'
        )->will(
            $this->returnValue('en')
        );

        $this->quoteAddressMock->expects($this->any())->method('getVatId')->will($this->returnValue('someVatID'));


        $this->quoteAddressMock->expects($this->once())->method('save');

        $this->assertEquals(
            $this->validationResult,
            $this->model->validate($this->quoteAddressMock, $this->storeMock)
        );
    }

    public function testIsEnabledWithBillingTaxCalculationAddressType()
    {
        $this->customerAddressMock->expects(
            $this->any()
        )->method(
            'isVatValidationEnabled'
        )->will(
            $this->returnValue(true)
        );

        $this->customerAddressMock->expects(
            $this->any()
        )->method(
            'getTaxCalculationAddressType'
        )->will(
            $this->returnValue(\Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING)
        );

        $this->quoteAddressMock->expects(
            $this->any()
        )->method(
            'getAddressType'
        )->will(
            $this->returnValue(\Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING)
        );

        $this->model->isEnabled($this->quoteAddressMock, $this->storeMock);
    }

    public function testIsEnabledWithEnabledVatValidation()
    {
        $this->customerAddressMock->expects(
            $this->any()
        )->method(
            'isVatValidationEnabled'
        )->will(
            $this->returnValue(true)
        );
        $this->model->isEnabled($this->quoteAddressMock, $this->storeMock);
    }
}
