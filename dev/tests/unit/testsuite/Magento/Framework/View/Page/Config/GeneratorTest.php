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
 * Test for page config generator model
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var \Magento\Framework\View\Page\Config\Structure|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $structureMock;

    /**
     * @var \Magento\Framework\View\Page\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->structureMock = $this->getMockBuilder('Magento\Framework\View\Page\Config\Structure')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->setMethods(['getMetadata', 'renderTitle', 'prepareFavicon', 'renderAssets', 'getTitle', 'getDefaultTitle',
                    'getFaviconFile', 'addPageAsset', 'getAssetCollection', 'processMerge', 'getMetadataTemplate',
                    'processMetadataContent', 'addRemotePageAsset', 'setTitle'])->disableOriginalConstructor()
            ->getMock();
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->generator = $this->objectManagerHelper->getObject(
            'Magento\Framework\View\Page\Config\Generator',
            [
                'structure' => $this->structureMock,
                'pageConfig' => $this->pageConfigMock,
            ]
        );
    }

    /**
     * @param [] $data
     * @dataProvider processDataProvider
     */
    public function testProcess($data)
    {
        $this->structureMock->expects($this->once())->method('getAssets')->will($this->returnValue($data));
        $this->structureMock->expects($this->once())
            ->method('getMetadata')
            ->will($this->returnValue(['name' => 'content']));
        $this->pageConfigMock->expects($this->once())->method('setTitle')->will($this->returnSelf());
        $this->pageConfigMock->expects($this->once())->method('addRemotePageAsset')->will($this->returnSelf());
        $this->assertInstanceOf('\Magento\Framework\View\Page\Config\Generator', $this->generator->process());
    }

    /**
     * @param [] $data
     * @dataProvider processDataProviderNegative
     */
    public function testProcessNegative($data)
    {
        $this->structureMock->expects($this->once())->method('getAssets')->will($this->returnValue($data));
        $this->structureMock->expects($this->once())
            ->method('getMetadata')
            ->will($this->returnValue(['name' => 'content']));
        $this->pageConfigMock->expects($this->once())->method('setTitle')->will($this->returnSelf());
        $this->pageConfigMock->expects($this->never())->method('addRemotePageAsset');
        $this->assertInstanceOf('\Magento\Framework\View\Page\Config\Generator', $this->generator->process());
    }

    public function processDataProvider()
    {
        return [
            [
                ['name' => ['src_type' => 'controller']]
            ],
            [
                ['name' => ['src_type' => 'url']]
            ]
        ];
    }

    public function processDataProviderNegative()
    {
        return [
            [
                ['name' => ['test' => 'css']]
            ]
        ];
    }
}
