<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Paging;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;
use Magento\Ui\ContentType\ContentTypeFactory;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\ConfigFactory;
use Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

/**
 * Class ViewTest
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var ConfigBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationBuilderMock;

    /**
     * @var TemplateContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $renderContextMock;

    /**
     * @var ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configurationFactoryMock;

    /**
     * @var ContentTypeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeFactoryMock;

    /**
     * @var Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetRepoMock;

    public function setUp()
    {
        $this->configurationFactoryMock = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->renderContextMock = $this->getMock(
            'Magento\Framework\View\Element\UiComponent\Context',
            ['getNamespace', 'getStorage', 'getRequestParam'],
            [],
            '',
            false
        );
        $this->contextMock = $this->getMock(
            'Magento\Framework\View\Element\Template\Context',
            ['getAssetRepository'],
            [],
            '',
            false
        );
        $this->contentTypeFactoryMock = $this->getMock('Magento\Ui\ContentType\ContentTypeFactory', [], [], '', false);
        $this->configurationBuilderMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigBuilderInterface'
        );
        $this->assetRepoMock = $this->getMock('Magento\Framework\View\Asset\Repository', [], [], '', false);
        $this->contextMock->expects($this->any())->method('getAssetRepository')->willReturn($this->assetRepoMock);

        $this->view = new \Magento\Ui\Paging\View(
            $this->contextMock,
            $this->renderContextMock,
            $this->contentTypeFactoryMock,
            $this->configurationFactoryMock,
            $this->configurationBuilderMock
        );
    }

    public function testPrepare()
    {
        $paramsSize = 20;
        $paramsPage = 1;
        $nameSpace = 'namespace';
        $configurationMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigInterface',
            ['getData'],
            '',
            false
        );
        $this->renderContextMock->expects($this->any())->method('getNamespace')->willReturn($nameSpace);
        $this->configurationFactoryMock->expects($this->once())->method('create')->willReturn($configurationMock);

        $storageMock = $this->getMockForAbstractClass(
            'Magento\Framework\View\Element\UiComponent\ConfigStorageInterface'
        );
        $dataCollectionMock = $this->getMockForAbstractClass(
            'Magento\Framework\Api\CriteriaInterface',
            ['setLimit'],
            '',
            false
        );

        $this->renderContextMock->expects($this->any())->method('getStorage')->willReturn($storageMock);
        $storageMock->expects($this->once())
            ->method('addComponentsData')
            ->with($configurationMock)
            ->willReturnSelf();
        $storageMock->expects($this->once())->method('getDataCollection')->willReturn($dataCollectionMock);

        $configurationMock->expects($this->at(0))->method('getData')->with('current')->willReturn($paramsPage);
        $this->renderContextMock->expects($this->at(3))->method('getRequestParam')->with('page', $paramsPage)
            ->willReturn($paramsPage);

        $configurationMock->expects($this->at(1))->method('getData')->with('pageSize')->willReturn($paramsSize);
        $this->renderContextMock->expects($this->at(4))->method('getRequestParam')->with('limit')
            ->willReturn($paramsSize);

        $dataCollectionMock->expects($this->any())->method('setLimit')->with($paramsPage, $paramsSize)->willReturn(
            null
        );

        $this->assertNull($this->view->prepare());
    }
}
