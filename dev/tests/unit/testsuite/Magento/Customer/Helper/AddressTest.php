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

    /** @var \Magento\App\Helper\Context */
    protected $context;

    /** @var \Magento\View\Element\BlockFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $blockFactory;

    /** @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Core\Model\Store\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $coreStoreConfig;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $customerMetadataService;

    /** @var \Magento\Customer\Model\Address\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $addressConfig;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\App\Helper\Context')->disableOriginalConstructor()->getMock();
        $this->blockFactory = $this->getMockBuilder('Magento\View\Element\BlockFactory')
            ->disableOriginalConstructor()->getMock();
        $this->storeManager = $this->getMockBuilder('Magento\Core\Model\StoreManagerInterface')
            ->disableOriginalConstructor()->getMock();
        $this->coreStoreConfig = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()->getMock();
        $this->customerMetadataService = $this->getMockBuilder(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface'
        )->disableOriginalConstructor()->getMock();
        $this->addressConfig = $this->getMockBuilder('Magento\Customer\Model\Address\Config')
            ->disableOriginalConstructor()->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject('Magento\Customer\Helper\Address', [
            'context' => $this->context,
            'blockFactory' => $this->blockFactory,
            'storeManager' => $this->storeManager,
            'coreStoreConfig' => $this->coreStoreConfig,
            'customerMetadataService' => $this->customerMetadataService,
            'addressConfig' => $this->addressConfig
        ]);
    }

    /**
     * @param int $numLines
     * @param int $expectedNumLines
     * @dataProvider providerGetStreetLines
     */
    public function testGetStreetLines($numLines, $expectedNumLines) {
        $attributeMock = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata')
            ->disableOriginalConstructor()->getMock();
        $attributeMock->expects($this->any())->method('getMultilineCount')->will($this->returnValue($numLines));

        $this->customerMetadataService->expects($this->any())->method('getAttributeMetadata')
            ->will($this->returnValue($attributeMock));

        $store = $this->getMockBuilder('Magento\Core\Model\Store')->disableOriginalConstructor()->getMock();
        $this->storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));

        $this->assertEquals($expectedNumLines, $this->helper->getStreetLines());
    }

    public function providerGetStreetLines()
    {
        return [
            [-1, 2],
            [0, 2],
            [1, 1],
            [2, 2],
            [3, 3],
            [4, 4],
            [5, 4],
            [10, 4]
        ];
    }
}
