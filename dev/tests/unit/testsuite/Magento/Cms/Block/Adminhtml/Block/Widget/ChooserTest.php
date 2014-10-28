<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml\Block\Widget;

/**
 * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser
 */
class ChooserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser
     */
    protected $chooser;

    /**
     * @var \Magento\Backend\Block\Template\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Cms\Model\BlockFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockFactoryMock;

    /**
     * @var \Magento\Framework\Math\Random|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mathRandomMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $elementMock;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlBuilderMock;

    /**
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chooserMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->contextMock = $this->getMock(
            'Magento\Backend\Block\Template\Context',
            [
                'getMathRandom',
                'getUrlBuilder',
                'getLayout',
                'getFilesystem'
            ],
            [],
            '',
            false
        );
        $this->blockFactoryMock = $this->getMock(
            'Magento\Cms\Model\BlockFactory',
            [],
            [],
            '',
            false
        );
        $this->mathRandomMock = $this->getMock(
            'Magento\Framework\Math\Random',
            [
                'getUniqueHash'
            ],
            [],
            '',
            false
        );
        $this->elementMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            [
                'getId',
                'setData'
            ],
            [],
            '',
            false
        );
        $this->urlBuilderMock = $this->getMock(
            'Magento\Framework\UrlInterface',
            [],
            [],
            '',
            false
        );
        $this->layoutMock = $this->getMock(
            'Magento\Framework\View\LayoutInterface',
            [],
            [],
            '',
            false
        );
        $this->filesystemMock = $this->getMock(
            'Magento\Framework\Filesystem',
            [],
            [],
            '',
            false
        );
        $this->chooserMock = $this->getMock(
            'Magento\Framework\View\Element\BlockInterface',
            [
                'setElement',
                'setConfig',
                'setFieldsetId',
                'setSourceUrl',
                'setUniqId',
                'toHtml'
            ],
            [],
            '',
            false
        );
        $this->contextMock
            ->expects($this->once())
            ->method('getMathRandom')
            ->willReturn($this->mathRandomMock);
        $this->contextMock
            ->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);
        $this->contextMock
            ->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $this->contextMock
            ->expects($this->once())
            ->method('getFilesystem')
            ->willReturn($this->filesystemMock);
        $this->chooser = $objectManager->getObject(
            'Magento\Cms\Block\Adminhtml\Block\Widget\Chooser',
            [
                'context' => $this->contextMock,
                'blockFactory' => $this->blockFactoryMock
            ]
        );
    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser::prepareElementHtml
     * @dataProvider prepareElementHtmlDataProvider
     */
    public function testPrepareElementHtml($value)
    {
        $elementId = 1; //assumed
        $uniqId = '126hj4h3j73hk7b347jhkl37gb34'; //assumed
        $baseUrl = 'cms/block_widget/chooser';
        $sourceUrl = 'cms/block_widget/chooser/126hj4h3j73hk7b347jhkl37gb34'; //assumed
        $blockClass = 'Magento\Widget\Block\Adminhtml\Widget\Chooser';
        $config = ['key1' => 'value1']; //assumed
        $fieldsetId = 2; //assumed
        $html = 'some html'; //assumed
        $dataKey = 'after_element_html';
        $this->elementMock
            ->expects($this->once())
            ->method('getId')
            ->willReturn($elementId);
        $this->mathRandomMock
            ->expects($this->once())
            ->method('getUniqueHash')
            ->with($elementId)
            ->willReturn($uniqId);
        $this->urlBuilderMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($baseUrl, array('uniq_id' => $uniqId))
            ->willReturn($sourceUrl);
        $this->layoutMock
            ->expects($this->once())
            ->method('createBlock')
            ->with($blockClass)
            ->willReturn($this->chooserMock);
        $this->chooserMock
            ->expects($this->once())
            ->method('setElement')
            ->with($this->elementMock)
            ->willReturnSelf();
        $this->chooser->setConfig($config);
        $this->chooserMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($config)
            ->willReturnSelf();
        $this->chooser->setFieldsetId($fieldsetId);
        $this->chooserMock
            ->expects($this->once())
            ->method('setFieldsetId')
            ->with($fieldsetId)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->once())
            ->method('setSourceUrl')
            ->with($sourceUrl)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->once())
            ->method('setUniqId')
            ->with($uniqId)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->once())
            ->method('toHtml')
            ->willReturn($html);
        $this->elementMock
            ->expects($this->once())
            ->method('setData')
            ->with($dataKey, $html)
            ->willReturnSelf();

        $this->assertEquals($this->elementMock, $this->chooser->prepareElementHtml($this->elementMock));
    }

    public function prepareElementHtmlDataProvider()
    {
        return [
          [0],
          [1]
        ];
    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser::getRowClickCallback
     */
    public function testGetRowClickCallback()
    {

    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser::getGridUrl
     */
    public function testGetGridUrl()
    {

    }
}
