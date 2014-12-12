<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model\Balance;

class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerBalance\Model\Balance\History
     */
    protected $model;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $balanceModelMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $customerRegistryMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $transportBuilderMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $designMock;

    /**
     * @var \PHPUnit_FrameWork_MockObject_MockObject
     */
    protected $resourceMock;

    protected function setUp()
    {
        $this->balanceModelMock = $this->getMock(
            '\Magento\CustomerBalance\Model\Balance',
            ['getNotifyByEmail', 'getStoreId', 'getCustomer', 'getWebsiteId', 'getAmount'],
            [],
            '',
            false
        );
        $this->scopeConfigMock = $this->getMock(
            '\Magento\Framework\App\Config\ScopeConfigInterface',
            [],
            [],
            '',
            false
        );
        $this->transportBuilderMock = $this->getMock(
            '\Magento\Framework\Mail\Template\TransportBuilder',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\Resource\AbstractResource',
            [],
            '',
            false,
            false,
            true,
            ['getIdFieldName', 'markAsSent']
        );
        $this->customerRegistryMock = $this->getMock('\Magento\Customer\Model\CustomerRegistry', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->designMock = $this->getMock('\Magento\Framework\View\DesignInterface', [], [], '', false);
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectHelper->getObject(
            '\Magento\CustomerBalance\Model\Balance\History',
            [
                'customerRegistry' => $this->customerRegistryMock,
                'transportBuilder' => $this->transportBuilderMock,
                'scopeConfig' => $this->scopeConfigMock,
                'design' => $this->designMock,
                'storeManager' => $this->storeManagerMock,
                'resource' => $this->resourceMock
            ]
        );
    }

    public function testAfterSave()
    {
        $this->model->setBalanceModel($this->balanceModelMock);
        $customerId = 1;
        $storeId = 2;
        $websiteId = 3;
        $templateIdentifier = 'tpl';
        $area = 'area';
        $format = 'format';
        $amount = 10;
        $customerName = 'John Doe';
        $customerEmail = 'johndoe@example.com';

        $customerDataMock = $this->getMock('\Magento\Customer\Model\Data\Customer', ['getId'], [], '', false);
        $customerMock = $this->getMock('\Magento\Customer\Model\Customer', ['getEmail', 'getName'], [], '', false);
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $transportMock = $this->getMock('\Magento\Framework\Mail\TransportInterface', [], [], '', false);
        $websiteMock = $this->getMock('\Magento\Store\Model\Website', [], [], '', false);
        $currencyMock = $this->getMock('\Magento\Directory\Model\Currency', [], [], '', false);
        $this->balanceModelMock->expects($this->once())->method('getNotifyByEmail')->willReturn(true);
        $this->balanceModelMock->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->balanceModelMock->expects($this->once())->method('getCustomer')->willReturn($customerDataMock);
        $customerDataMock->expects($this->once())->method('getId')->willReturn($customerId);
        $this->customerRegistryMock->expects($this->once())->method('retrieve')->with($customerId)
            ->willReturn($customerMock);
        $this->scopeConfigMock->expects($this->exactly(2))->method('getValue')->withConsecutive(
            [
                'customer/magento_customerbalance/email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ],
            [
                'customer/magento_customerbalance/email_identity',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            ]
        )->willReturn($templateIdentifier);
        $this->transportBuilderMock->expects($this->once())->method('setTemplateIdentifier')->with($templateIdentifier)
            ->willReturnSelf();
        $this->designMock->expects($this->once())->method('getArea')->willReturn($area);
        $this->transportBuilderMock->expects($this->once())->method('setTemplateOptions')->with(
            ['area' => $area, 'store' => $storeId]
        )->willReturnSelf();
        $this->balanceModelMock->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->storeManagerMock->expects($this->once())->method('getWebsite')->with($websiteId)
            ->willReturn($websiteMock);
        $websiteMock->expects($this->once())->method('getBaseCurrency')->willReturn($currencyMock);
        $this->balanceModelMock->expects($this->once())->method('getAmount')->willReturn($amount);
        $currencyMock->expects($this->once())->method('format')->with($amount, [], false)->willReturn($format);
        $customerMock->expects($this->exactly(2))->method('getName')->willReturn($customerName);
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($storeMock);
        $this->transportBuilderMock->expects($this->once())->method('setTemplateVars')->with(
            ['balance' => $format, 'name' => $customerName, 'store' => $storeMock]
        )->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('setFrom')->with($templateIdentifier)
            ->willReturnSelf();
        $customerMock->expects($this->once())->method('getEmail')->willReturn($customerEmail);
        $this->transportBuilderMock->expects($this->once())->method('addTo')->with($customerEmail, $customerName)
            ->willReturnSelf();
        $this->transportBuilderMock->expects($this->once())->method('getTransport')->willReturn($transportMock);
        $transportMock->expects($this->once())->method('sendMessage');
        $this->assertEquals($this->model, $this->model->afterSave());
    }
}
