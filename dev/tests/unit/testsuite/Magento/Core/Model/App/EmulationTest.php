<?php
/**
 * Tests Magento\Core\Model\App\Emulation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\App;

class EmulationTest extends \Magento\Test\BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\TranslateInterface
     */
    private $translateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Locale\ResolverInterface
     */
    private $localeResolverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Design
     */
    private $designMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Translate\Inline\ConfigInterface
     */
    private $inlineConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\View\DesignInterface
     */
    private $viewDesignMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\Store
     */
    private $storeMock;

    /**
     * @var \Magento\Core\Model\App\Emulation
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        // Mocks
        $this->designMock = $this->basicMock('Magento\Core\Model\Design');
        $this->storeManagerMock = $this->basicMock('Magento\Framework\StoreManagerInterface');
        $this->translateMock = $this->basicMock('Magento\Framework\TranslateInterface');
        $this->scopeConfigMock = $this->basicMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->localeResolverMock = $this->basicMock('Magento\Framework\Locale\ResolverInterface');
        $this->inlineConfigMock = $this->basicMock('Magento\Framework\Translate\Inline\ConfigInterface');
        $this->inlineTranslationMock = $this->basicMock('Magento\Framework\Translate\Inline\StateInterface');
        $this->viewDesignMock = $this->getMockForAbstractClass('Magento\Framework\View\DesignInterface');
        $this->storeMock = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            -setMethods([])
            ->getMock();

        // Stubs
        $this->designMock->expects($this->any())->method('loadChange')->willReturnSelf();
        $this->designMock->expects($this->any())->method('getData')->willReturn(false);
        $this->translateMock->expects($this->any())->method('setLocale')->willReturnSelf();

        // Prepare SUT
        $this->model = $this->objectManager->getObject('Magento\Core\Model\App\Emulation',
            [
                'storeManager' => $this->storeManagerMock,
                'viewDesign' => $this->viewDesignMock,
                'design' => $this->designMock,
                'translate' => $this->translateMock,
                'scopeConfig' => $this->scopeConfigMock,
                'inlineConfig' => $this->inlineConfigMock,
                'inlineTranslation' => $this->inlineTranslationMock,
                'localeResolver' => $this->localeResolverMock,
            ]
        );
    }

    public function testStartEnvironmentEmulationSameStore()
    {
        $this->storeManagerMock->expects($this->once())->method('getStore')->willReturn($this->storeMock);
    }

} 