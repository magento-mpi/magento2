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

class EmulationTest extends \Magento\Test\Helper
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Store\Model\StoreManagerInterface
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
     * @var \Magento\Core\Model\App\Emulation
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        // Mocks
        $this->designMock = $this->basicMock('Magento\Core\Model\Design');
        $this->storeManagerMock = $this->basicMock('Magento\Store\Model\StoreManagerInterface');
        $this->translateMock = $this->basicMock('Magento\Framework\TranslateInterface');
        $this->scopeConfigMock = $this->basicMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->localeResolverMock = $this->basicMock('Magento\Framework\Locale\ResolverInterface');
        $this->inlineConfigMock = $this->basicMock('Magento\Framework\Translate\Inline\ConfigInterface');
        $this->inlineTranslationMock = $this->basicMock('Magento\Framework\Translate\Inline\StateInterface');
        $this->viewDesignMock = $this->getMockForAbstractClass('Magento\Framework\View\DesignInterface');

        // Stubs
        $this->basicStub($this->designMock, 'loadChange')->willReturnSelf();
        $this->basicStub($this->designMock, 'getData')->willReturn(false);
        $this->basicStub($this->translateMock, 'setLocale')->willReturnSelf();

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

    public function testStartDefaults()
    {
        // Test data
        $newDesignData = ['array', 'with', 'data'];
        $inlineTranslate = false;
        $initArea = 'initial area';
        $initTheme = 'initial design theme';
        $initStore = 1;
        $initLocale = 'initial locale code';
        $newInlineTranslate = false;
        $newLocale = 'new locale code';
        $newStoreId = 9;
        $initDesignData = ['area' => $initArea, 'theme' => $initTheme, 'store' => $initStore];

        // Stubs

        $this->basicStub($this->inlineTranslationMock, 'isEnabled')->willReturn($inlineTranslate);
        $this->basicStub($this->viewDesignMock, 'getArea')->willReturn($initArea);
        $this->basicStub($this->viewDesignMock, 'getDesignTheme')->willReturn($initTheme);
        $this->basicStub($this->storeManagerMock, 'getStore')->willReturn($initStore);
        $this->basicStub($this->localeResolverMock, 'getLocaleCode')->willReturn($initLocale);
        $this->basicStub($this->inlineConfigMock, 'isActive')->willReturn($newInlineTranslate);
        $this->basicStub($this->viewDesignMock, 'getConfigurationDesignTheme')->willReturn($newDesignData);
        $this->basicStub($this->scopeConfigMock, 'getValue')->willReturn($newLocale);

        // Expectations
        $this->once();
        $this->inlineTranslationMock->expects($this->once())->method('suspend')->with($newInlineTranslate);
        $this->viewDesignMock->expects($this->once())->method('setDesignTheme')->with($newDesignData);
        $this->localeResolverMock->expects($this->once())->method('setLocaleCode')->with($newLocale);
        $this->translateMock->expects($this->once())->method('setLocale')->with($newLocale);
        $this->storeManagerMock->expects($this->once())->method('setCurrentStore')->with($newStoreId);

        // Test
        $initialEnvironment = $this->model->startEnvironmentEmulation($newStoreId);
        $this->assertEquals($inlineTranslate, $initialEnvironment->getInitialTranslateInline());
        $this->assertEquals($initDesignData, $initialEnvironment->getInitialDesign());
        $this->assertEquals($initLocale, $initialEnvironment->getInitialLocaleCode());
    }

    public function testStartWithInlineTranslation()
    {
        $inlineTranslation = true;

        $this->basicStub($this->inlineConfigMock, 'isActive')->willReturn($inlineTranslation);

        $this->inlineTranslationMock->expects($this->once())
            ->method('suspend')
            ->with($inlineTranslation);

        $this->model->startEnvironmentEmulation(1, null, true);

    }

    public function testStartAreaNotDefault()
    {
        $area = 'backend';
        $newDesignData = ['array', 'with', 'data'];

        $this->basicStub($this->viewDesignMock, 'getConfiguratioNDesignTheme')->willReturn($newDesignData);

        $this->viewDesignMock->expects($this->once())
            ->method('setDesignTheme')
            ->with($newDesignData, $area);

        $this->model->startEnvironmentEmulation(1, $area);
    }

    public function testStop()
    {
        // Test data
        $initialEnvInfo = $this->objectManager->getObject('\Magento\Framework\Object');
        $initArea = 'initial area';
        $initTheme = 'initial design theme';
        $initStore = 1;
        $initLocale = 'initial locale code';
        $initTranslateInline = false;
        $initDesignData = ['area' => $initArea, 'theme' => $initTheme, 'store' => $initStore];
        $initialEnvInfo->setInitialTranslateInline($initTranslateInline)
            ->setInitialDesign($initDesignData)
            ->setInitialLocaleCode($initLocale);

        // Expectations
        $this->inlineTranslationMock->expects($this->once())
            ->method('resume')
            ->with($initTranslateInline);
        $this->viewDesignMock->expects($this->once())
            ->method('setDesignTheme')
            ->with($initTheme, $initArea);
        $this->storeManagerMock->expects($this->once())
            ->method('setCurrentStore')
            ->with($initStore);
        $this->localeResolverMock->expects($this->once())
            ->method('setLocaleCode')
            ->with($initLocale);
        $this->translateMock->expects($this->once())
            ->method('setLocale')
            ->with($initLocale);
        
        // Test
        $this->model->stopEnvironmentEmulation($initialEnvInfo);
    }
} 