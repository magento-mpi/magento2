<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Observer\Frontend\Quote\Address;

class CollectTotalsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeId;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $vatValidatorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $observerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilderMock;

    protected function setUp()
    {
        $this->storeId = 1;
        $this->customerDataMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\Customer',
            array('getStoreId', 'getCustomAttribute', 'getId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->customerAddressMock = $this->getMock('Magento\Customer\Helper\Address', array(), array(), '', false);
        $this->customerHelperMock = $this->getMock('Magento\Customer\Helper\Data', array(), array(), '', false);
        $this->customerBuilderMock = $this->getMock(
            'Magento\Customer\Service\V1\Data\CustomerBuilder',
            array('mergeDataObjectWithArray'),
            array(),
            '',
            false
        );
        $this->vatValidatorMock = $this->getMock(
            'Magento\Sales\Model\Observer\Frontend\Quote\Address\VatValidator',
            array(),
            array(),
            '',
            false
        );
        $this->observerMock = $this->getMock('\Magento\Event\Observer', array('getQuoteAddress'), array(), '', false);

        $this->quoteAddressMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address',
            array('getCountryId', 'getVatId', 'getQuote', 'setPrevQuoteCustomerGroupId', '__wakeup'),
            array(),
            '',
            false,
            false
        );


        $this->quoteMock = $this->getMock(
            'Magento\Sales\Model\Quote',
            array('setCustomerGroupId', 'getCustomerGroupId', 'getCustomerData', 'setCustomerData', '__wakeup'),
            array(),
            '',
            false
        );
        $this->observerMock->expects(
            $this->any()
        )->method(
            'getQuoteAddress'
        )->will(
            $this->returnValue($this->quoteAddressMock)
        );

        $this->quoteAddressMock->expects($this->any())->method('getQuote')->will($this->returnValue($this->quoteMock));

        $this->quoteMock->expects(
            $this->any()
        )->method(
            'getCustomerData'
        )->will(
            $this->returnValue($this->customerDataMock)
        );

        $this->customerDataMock->expects($this->any())->method('getStoreId')->will($this->returnValue($this->storeId));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->model = $objectManager->getObject(
            'Magento\Sales\Model\Observer\Frontend\Quote\Address\CollectTotals',
            array(
                'customerAddressHelper' => $this->customerAddressMock,
                'customerHelper' => $this->customerHelperMock,
                'vatValidator' => $this->vatValidatorMock,
                'customerBuilder' => $this->customerBuilderMock
            )
        );
    }

    public function testDispatchWithDisableAutoGroupChange()
    {
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'getCustomAttribute'
        )->with(
            'disable_auto_group_change'
        )->will(
            $this->returnValue(true)
        );

        $this->model->dispatch($this->observerMock);
    }

    public function testDispatchWithDisableVatValidator()
    {
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'getCustomAttribute'
        )->with(
            'disable_auto_group_change'
        )->will(
            $this->returnValue(false)
        );

        $this->vatValidatorMock->expects(
            $this->once()
        )->method(
            'isEnabled'
        )->with(
            $this->quoteAddressMock,
            $this->storeId
        )->will(
            $this->returnValue(false)
        );
        $this->model->dispatch($this->observerMock);
    }

    public function testDispatchWithCustomerCountryNotInEUAndNotLoggedCustomerInGroup()
    {
        /** Preconditions */
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'getCustomAttribute'
        )->with(
            'disable_auto_group_change'
        )->will(
            $this->returnValue(false)
        );

        $this->vatValidatorMock->expects(
            $this->once()
        )->method(
            'isEnabled'
        )->with(
            $this->quoteAddressMock,
            $this->storeId
        )->will(
            $this->returnValue(true)
        );

        $this->quoteAddressMock->expects(
            $this->once()
        )->method(
            'getCountryId'
        )->will(
            $this->returnValue('customerCountryCode')
        );
        $this->quoteAddressMock->expects($this->once())->method('getVatId')->will($this->returnValue('vatId'));

        $this->customerHelperMock->expects(
            $this->once()
        )->method(
            'isCountryInEU'
        )->with(
            'customerCountryCode'
        )->will(
            $this->returnValue(false)
        );

        $this->customerDataMock->expects($this->once())->method('getId')->will($this->returnValue(null));

        /** Assertions */
        $this->quoteAddressMock->expects($this->never())->method('setPrevQuoteCustomerGroupId');
        $this->customerBuilderMock->expects($this->never())->method('mergeDataObjectWithArray');
        $this->quoteMock->expects($this->never())->method('setCustomerGroupId');

        /** SUT execution */
        $this->model->dispatch($this->observerMock);
    }

    public function testDispatchWithDefaultCustomerGroupId()
    {
        /** Preconditions */
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'getCustomAttribute'
        )->with(
            'disable_auto_group_change'
        )->will(
            $this->returnValue(false)
        );

        $this->vatValidatorMock->expects(
            $this->once()
        )->method(
            'isEnabled'
        )->with(
            $this->quoteAddressMock,
            $this->storeId
        )->will(
            $this->returnValue(true)
        );

        $this->quoteAddressMock->expects(
            $this->once()
        )->method(
            'getCountryId'
        )->will(
            $this->returnValue('customerCountryCode')
        );
        $this->quoteAddressMock->expects($this->once())->method('getVatId')->will($this->returnValue(null));

        $this->quoteMock->expects(
            $this->once()
        )->method(
            'getCustomerGroupId'
        )->will(
            $this->returnValue('customerGroupId')
        );

        $this->customerDataMock->expects($this->once())->method('getId')->will($this->returnValue('1'));
        $this->customerHelperMock->expects(
            $this->once()
        )->method(
            'getDefaultCustomerGroupId'
        )->will(
            $this->returnValue('defaultCustomerGroupId')
        );

        /** Assertions */
        $this->quoteAddressMock->expects(
            $this->once()
        )->method(
            'setPrevQuoteCustomerGroupId'
        )->with(
            'customerGroupId'
        );
        $this->quoteMock->expects($this->once())->method('setCustomerGroupId')->with('defaultCustomerGroupId');
        $this->customerBuilderMock->expects(
            $this->once()
        )->method(
            'mergeDataObjectWithArray'
        )->with(
            $this->customerDataMock,
            array('group_id' => 'defaultCustomerGroupId')
        )->will(
            $this->returnValue($this->customerDataMock)
        );

        $this->quoteMock->expects($this->once())->method('setCustomerData')->with($this->customerDataMock);

        /** SUT execution */
        $this->model->dispatch($this->observerMock);
    }

    public function testDispatchWithCustomerCountryInEU()
    {
        /** Preconditions */
        $this->customerDataMock->expects(
            $this->once()
        )->method(
            'getCustomAttribute'
        )->with(
            'disable_auto_group_change'
        )->will(
            $this->returnValue(false)
        );

        $this->vatValidatorMock->expects(
            $this->once()
        )->method(
            'isEnabled'
        )->with(
            $this->quoteAddressMock,
            $this->storeId
        )->will(
            $this->returnValue(true)
        );

        $this->quoteAddressMock->expects(
            $this->once()
        )->method(
            'getCountryId'
        )->will(
            $this->returnValue('customerCountryCode')
        );
        $this->quoteAddressMock->expects($this->once())->method('getVatId')->will($this->returnValue('vatID'));

        $this->customerHelperMock->expects(
            $this->once()
        )->method(
            'isCountryInEU'
        )->with(
            'customerCountryCode'
        )->will(
            $this->returnValue(true)
        );

        $this->quoteMock->expects(
            $this->once()
        )->method(
            'getCustomerGroupId'
        )->will(
            $this->returnValue('customerGroupId')
        );

        $validationResult = array('some' => 'result');
        $this->vatValidatorMock->expects(
            $this->once()
        )->method(
            'validate'
        )->with(
            $this->quoteAddressMock,
            $this->storeId
        )->will(
            $this->returnValue($validationResult)
        );

        $this->customerHelperMock->expects(
            $this->once()
        )->method(
            'getCustomerGroupIdBasedOnVatNumber'
        )->with(
            'customerCountryCode',
            $validationResult,
            $this->storeId
        )->will(
            $this->returnValue('customerGroupId')
        );

        /** Assertions */
        $this->quoteAddressMock->expects(
            $this->once()
        )->method(
            'setPrevQuoteCustomerGroupId'
        )->with(
            'customerGroupId'
        );
        $this->quoteMock->expects($this->once())->method('setCustomerGroupId')->with('customerGroupId');
        $this->customerBuilderMock->expects(
            $this->once()
        )->method(
            'mergeDataObjectWithArray'
        )->with(
            $this->customerDataMock,
            array('group_id' => 'customerGroupId')
        )->will(
            $this->returnValue($this->customerDataMock)
        );

        /** SUT execution */
        $this->model->dispatch($this->observerMock);
    }
}
