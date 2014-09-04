<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\View\Page\Config;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Test for page config renderer model
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /**
     * @var \Magento\Framework\View\Asset\MinifyService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetMinifyServiceMock;

    /**
     * @var \Magento\Framework\View\Asset\AssetInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetInterfaceMock;

    /**
     * @var \Magento\Framework\View\Asset\MergeService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetMergeServiceMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $escaperMock;

    /**
     * @var \Magento\Framework\Stdlib\String|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringMock;

    /**
     * @var \Magento\Framework\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Framework\View\Asset\GroupedCollection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $assetsCollection;

    /**
     * @var \Magento\Framework\View\Asset\PropertyGroup|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyGroupMock;

    /**
     * @var \Magento\Framework\App\Action\Title|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $titlesMock;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->setMethods(
                [
                    'getMetadata',
                    'renderTitle',
                    'prepareFavicon',
                    'renderAssets',
                    'getTitle',
                    'getDefaultTitle',
                    'getFaviconFile',
                    'addPageAsset',
                    'getAssetCollection',
                    'processMerge',
                    'getMetadataTemplate',
                    'processMetadataContent',
                    'addRemotePageAsset',
                    'setTitle',
                    'getIncludes',
                    'getTranslatorScript'
                ]
            )->disableOriginalConstructor()
            ->getMock();

        $this->assetMinifyServiceMock = $this->getMockBuilder('Magento\Framework\View\Asset\MinifyService')
            ->setMethods(['getAssets'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetMergeServiceMock = $this->getMockBuilder('Magento\Framework\View\Asset\MergeService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlBuilderMock = $this->getMockForAbstractClass('Magento\Framework\UrlInterface');

        $this->escaperMock = $this->getMockBuilder('Magento\Framework\Escaper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->stringMock = $this->getMockBuilder('Magento\Framework\Stdlib\String')
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder('Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetsCollection = $this->getMockBuilder('Magento\Framework\View\Asset\GroupedCollection')
            ->setMethods(['getGroups'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->propertyGroupMock = $this->getMockBuilder('Magento\Framework\View\Asset\PropertyGroup')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetInterfaceMock = $this->getMockForAbstractClass('Magento\Framework\View\Asset\AssetInterface');

        $this->titlesMock = $this->getMockBuilder('Magento\Framework\App\Action\Title')
            ->setMethods(['add', 'get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->renderer = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Page\Config\Renderer',
            [
                'pageConfig' => $this->pageConfigMock,
                'assetMinifyService' => $this->assetMinifyServiceMock,
                'assetMergeService' => $this->assetMergeServiceMock,
                'urlBuilder' => $this->urlBuilderMock,
                'escaper' => $this->escaperMock,
                'string' => $this->stringMock,
                'logger' => $this->loggerMock,
                'titles' => $this->titlesMock
            ]
        );
    }

    public function testRenderHeadContent()
    {
        $title = 'some_title';
        $someUrl = 'some_url';
        $expected = '<meta name="name" content="value"/>' . "\n"
            . "<title>some_title</title>" . "\n" . '<link  href="' . $someUrl . '" />' . "\n";
        $this->pageConfigMock
            ->expects($this->once())
            ->method('getMetadata')
            ->will($this->returnValue(['name' => 'value']));
        $this->pageConfigMock
            ->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->pageConfigMock
            ->expects($this->exactly(2))
            ->method('getTitle')
            ->will($this->returnValue($title));
        $this->titlesMock
            ->expects($this->once())
            ->method('add')
            ->will($this->returnSelf());
        $this->titlesMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue([$title]));
        $this->pageConfigMock
            ->expects($this->once())
            ->method('setTitle')
            ->will($this->returnSelf());
        $this->assetsCollection
            ->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$this->propertyGroupMock]));
        $this->assetInterfaceMock
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($someUrl));
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will($this->returnValue([$this->assetInterfaceMock]));
        $this->assertEquals($expected, $this->renderer->renderHeadContent());
    }

    /**
     * @param string $metadataTemplate
     * @param string $expected
     *
     * @dataProvider renderMetadataDataProvider
     */
    public function testRenderMetadata($metadataTemplate, $expected)
    {
        $this->pageConfigMock
            ->expects($this->once())
            ->method('getMetadata')
            ->will($this->returnValue([$metadataTemplate => '%content']));
        $this->assertEquals($expected, $this->renderer->renderMetadata());
    }

    public function renderMetadataDataProvider()
    {
        return [
            ['charset', '<meta charset="%content"/>' . "\n"],
            ['content_type', '<meta http-equiv="Content-Type" content="%content"/>' . "\n"],
            ['x_ua_compatible', '<meta http-equiv="X-UA-Compatible" content="%content"/>' . "\n"],
            ['media_type', false],
            ['name', '<meta name="name" content="%content"/>' . "\n"]
        ];
    }

    public function testRenderTitle()
    {
        $title = 'some_title';
        $expected = "<title>some_title</title>" . "\n";
        $this->pageConfigMock
            ->expects($this->exactly(2))
            ->method('getTitle')
            ->will($this->returnValue($title));
        $this->titlesMock
            ->expects($this->once())
            ->method('add')
            ->will($this->returnSelf());
        $this->titlesMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue([$title]));
        $this->pageConfigMock
            ->expects($this->once())
            ->method('setTitle')
            ->will($this->returnSelf());

        $this->assertEquals($expected, $this->renderer->renderTitle());
    }

    public function testRenderAsset()
    {
        $expectedResult = '<link  href="" />' . "\n" . '<link  href="" />' . "\n";
        $this->pageConfigMock->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->assetsCollection->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$this->propertyGroupMock]));
        $groupAssets = [$this->assetInterfaceMock, $this->assetInterfaceMock];
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will($this->returnValue($groupAssets));
        $this->propertyGroupMock->expects($this->at(0))
            ->method('getProperty')
            ->will($this->returnValue(['attributes']));
        $this->propertyGroupMock->expects($this->at(0))
            ->method('getProperty')
            ->will($this->returnValue(['ie_condition']));
        $this->assertEquals($expectedResult, $this->renderer->renderAssets());
    }

    public function testRenderAssetsException()
    {

        $exception = new \Magento\Framework\Exception('my message');
        $this->pageConfigMock->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->assetsCollection->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$this->propertyGroupMock]));
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will($this->returnValue([$this->assetInterfaceMock]));
        $this->assetInterfaceMock->expects($this->once())->method('getUrl')->will($this->throwException($exception));
        $this->loggerMock->expects($this->once())->method('logException')->with($exception);
        $this->renderer->renderAssets();
    }

    public function testProcessMerge()
    {
        $contentType = 'css';
        $expectedResult = '<link  rel="stylesheet" type="text/css"  media="all" href="" />' . "\n"
            . '<link  rel="stylesheet" type="text/css"  media="all" href="" />' . "\n";

        $this->pageConfigMock->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->assetsCollection->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$this->propertyGroupMock]));
        $groupAssets = [$this->assetInterfaceMock, $this->assetInterfaceMock];
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will($this->returnValue($groupAssets));
        $this->propertyGroupMock->expects($this->any())->method('getProperty')
            ->will(
                $this->returnValueMap(
                    [
                        ['can_merge', true],
                        ['content_type', $contentType]
                    ]
                )
            );
        $this->assetMergeServiceMock
            ->expects($this->once())
            ->method('getMergedAssets')
            ->with($groupAssets, $contentType)
            ->will($this->returnValue($groupAssets));
        $this->assertEquals($expectedResult, $this->renderer->renderAssets());
    }

    public function testGetGroupAttributes()
    {
        $contentType = 'js';
        $expectedResult = '<script  type="text/javascript"  src=""></script>' . "\n"
            . '<script  type="text/javascript"  src=""></script>' . "\n";
        $this->pageConfigMock->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->assetsCollection->expects($this->once())
            ->method('getGroups')
            ->will($this->returnValue([$this->propertyGroupMock]));
        $groupAssets = [$this->assetInterfaceMock, $this->assetInterfaceMock];
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will($this->returnValue($groupAssets));
        $this->propertyGroupMock->expects($this->any())->method('getProperty')
            ->will(
                $this->returnValueMap(
                    [
                        ['can_merge', true],
                        ['content_type', $contentType]
                    ]
                )
            );
        $this->assetMergeServiceMock
            ->expects($this->once())
            ->method('getMergedAssets')
            ->with($groupAssets, $contentType)
            ->will($this->returnValue($groupAssets));
        $this->assertEquals($expectedResult, $this->renderer->renderAssets());
    }

    public function testPrepareFavicon()
    {
        $this->pageConfigMock->expects($this->exactly(3))->method('getFaviconFile')->will($this->returnValue('file'));
        $this->pageConfigMock->expects($this->exactly(2))->method('addRemotePageAsset')->will($this->returnSelf());
        $this->assertEquals('', $this->renderer->prepareFavicon());
    }

    public function testGetAttributes()
    {
        $expectedResult = '<link  0="" href="" />' . "\n" . '<link  0="" href="" />' . "\n";
        $this->pageConfigMock->expects($this->once())
            ->method('getAssetCollection')
            ->will($this->returnValue($this->assetsCollection));
        $this->propertyGroupMock->expects($this->at(0))
            ->method('getProperty')
            ->will($this->returnValue(['attributes']));
        $this->propertyGroupMock->expects($this->at(1))
            ->method('getProperty')
            ->will($this->returnValue(['attributes']));
        $this->propertyGroupMock->expects($this->at(2))
            ->method('getProperty')
            ->will($this->returnValue(['attributes']));
        $this->propertyGroupMock->expects($this->at(3))
            ->method('getProperty')
            ->will($this->returnValue(['attributes']));
        $this->propertyGroupMock->expects($this->at(4))->method('getProperty')->will(
            $this->returnValue('ie_condition')
        );
        $this->assetsCollection->expects($this->once())
            ->method('getGroups')
            ->will(
                $this->returnValue([$this->propertyGroupMock])
            );
        $groupAssets = [$this->assetInterfaceMock, $this->assetInterfaceMock];
        $this->assetMinifyServiceMock
            ->expects($this->once())
            ->method('getAssets')
            ->will(
                $this->returnValue($groupAssets)
            );
        $this->assetMergeServiceMock
            ->expects($this->once())
            ->method('getMergedAssets')
            ->will(
                $this->returnValue($groupAssets)
            );
        $this->assertEquals($expectedResult, $this->renderer->renderAssets());
    }
}
