<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Helper\Address|\PHPUnit_Framework_MockObject_MockObject */
    protected $helper;

    /** @var \Magento\Framework\App\Helper\Context */
    protected $context;

    /** @var \Magento\View\Element\BlockFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $blockFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $scopeConfig;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerMetadataService;

    /** @var \Magento\Customer\Model\Address\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $addressConfig;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Framework\App\Helper\Context')->disableOriginalConstructor()->getMock();
        $this->blockFactory = $this->getMockBuilder(
            'Magento\View\Element\BlockFactory'
        )->disableOriginalConstructor()->getMock();
        $this->storeManager = $this->getMockBuilder(
            'Magento\Store\Model\StoreManagerInterface'
        )->disableOriginalConstructor()->getMock();
        $this->scopeConfig = $this->getMockBuilder(
            'Magento\Framework\App\Config\ScopeConfigInterface'
        )->disableOriginalConstructor()->getMock();
        $this->customerMetadataService = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface'
        )->disableOriginalConstructor()->getMock();
        $this->addressConfig = $this->getMockBuilder(
            'Magento\Customer\Model\Address\Config'
        )->disableOriginalConstructor()->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject(
            'Magento\Customer\Helper\Address',
            array(
                'context' => $this->context,
                'blockFactory' => $this->blockFactory,
                'storeManager' => $this->storeManager,
                'scopeConfig' => $this->scopeConfig,
                'customerMetadataService' => $this->customerMetadataService,
                'addressConfig' => $this->addressConfig
            )
        );
    }

    /**
     * @param int $numLines
     * @param int $expectedNumLines
     * @dataProvider providerGetStreetLines
     */
    public function testGetStreetLines($numLines, $expectedNumLines)
    {
        $attributeMock = $this->getMockBuilder(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata'
        )->disableOriginalConstructor()->getMock();
        $attributeMock->expects($this->any())->method('getMultilineCount')->will($this->returnValue($numLines));

        $this->customerMetadataService->expects(
            $this->any()
        )->method(
            'getAttributeMetadata'
        )->will(
            $this->returnValue($attributeMock)
        );

        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->assertEquals($expectedNumLines, $this->helper->getStreetLines());
    }

    public function providerGetStreetLines()
    {
        return array(
            array(-1, 2),
            array(0, 2),
            array(1, 1),
            array(2, 2),
            array(3, 3),
            array(4, 4),
            array(5, 4),
            array(10, 4)
        );
    }
}
