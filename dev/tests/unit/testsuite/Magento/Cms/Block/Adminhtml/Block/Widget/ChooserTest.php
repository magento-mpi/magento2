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
     * @var \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $thisMock;

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
     * @var \Magento\Framework\View\LayoutInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutMock;

    /**
     * @var \Magento\Cms\Model\Block|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $modelBlockMock;

    /**
     * @var \Magento\Framework\View\Element\BlockInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $chooserMock;

    protected function setUp()
    {
        $this->blockFactoryMock = $this
            ->getMockBuilder('Magento\Cms\Model\BlockFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mathRandomMock = $this
            ->getMockBuilder('Magento\Framework\Math\Random')
            ->disableOriginalConstructor()
            ->getMock();
        $this->elementMock = $this
            ->getMockBuilder('Magento\Framework\Data\Form\Element\AbstractElement')
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutMock = $this
            ->getMockBuilder('Magento\Framework\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->modelBlockMock = $this
            ->getMockBuilder('Magento\Cms\Model\Block')
            ->disableOriginalConstructor()
            ->getMock();
        $this->chooserMock = $this
            ->getMockBuilder('Magento\Framework\View\Element\BlockInterface')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setElement',
                    'setConfig',
                    'setFieldsetId',
                    'setSourceUrl',
                    'setUniqId',
                    'toHtml'
                ]
            )
            ->getMock();
        $this->thisMock = $this
            ->getMockBuilder('Magento\Cms\Block\Adminhtml\Block\Widget\Chooser')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getUrl',
                    'getLayout'
                ]
            )
            ->getMock();

        $this->thisMock
            ->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        $reflection = new \ReflectionClass($this->thisMock);
        $mathRandomProperty = $reflection->getProperty('mathRandom');
        $mathRandomProperty->setAccessible(true);
        $mathRandomProperty->setValue($this->thisMock, $this->mathRandomMock);
    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser::prepareElementHtml
     */
    public function testPrepareElementHtml()
    {
        $elementId = 1;
        $uniqId = '126hj4h3j73hk7b347jhkl37gb34';
        $sourceUrl = 'cms/block_widget/chooser/126hj4h3j73hk7b347jhkl37gb34';
        $config = ['key1' => 'value1'];
        $fieldsetId = 2;
        $html = 'some html';
        $title = 'some title';
        $elementValue = 'some element value';
        $modelBlockId = 3;

        $this->thisMock->setConfig($config);
        $this->thisMock->setFieldsetId($fieldsetId);

        $this->elementMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn($elementId);
        $this->mathRandomMock
            ->expects($this->any())
            ->method('getUniqueHash')
            ->with($elementId)
            ->willReturn($uniqId);
        $this->thisMock
            ->expects($this->any())
            ->method('getUrl')
            ->with('cms/block_widget/chooser', array('uniq_id' => $uniqId))
            ->willReturn($sourceUrl);
        $this->layoutMock
            ->expects($this->any())
            ->method('createBlock')
            ->with('Magento\Widget\Block\Adminhtml\Widget\Chooser')
            ->willReturn($this->chooserMock);
        $this->chooserMock
            ->expects($this->any())
            ->method('setElement')
            ->with($this->elementMock)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->any())
            ->method('setConfig')
            ->with($config)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->any())
            ->method('setFieldsetId')
            ->with($fieldsetId)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->any())
            ->method('setSourceUrl')
            ->with($sourceUrl)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->any())
            ->method('setUniqId')
            ->with($uniqId)
            ->willReturnSelf();
        $this->elementMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn($elementValue);
        $this->blockFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->modelBlockMock);
        $this->modelBlockMock
            ->expects($this->any())
            ->method('load')
            ->with($elementValue)
            ->willReturnSelf();
        $this->modelBlockMock
            ->expects($this->any())
            ->method('getId')
            ->willReturn($modelBlockId);
        $this->modelBlockMock
            ->expects($this->any())
            ->method('getTitle')
            ->willReturn($title);
        $this->chooserMock
            ->expects($this->any())
            ->method('setLabel')
            ->with($title)
            ->willReturnSelf();
        $this->chooserMock
            ->expects($this->any())
            ->method('toHtml')
            ->willReturn($html);
        $this->elementMock
            ->expects($this->any())
            ->method('setData')
            ->with('after_element_html', $html)
            ->willReturnSelf();

        $this->assertEquals($this->elementMock, $this->thisMock->prepareElementHtml($this->elementMock));
    }

    /**
     * @covers \Magento\Cms\Block\Adminhtml\Block\Widget\Chooser::getGridUrl
     */
    public function testGetGridUrl()
    {
        $url = 'some url';

        $this->thisMock
            ->expects($this->any())
            ->method('getUrl')
            ->with('cms/block_widget/chooser', ['_current' => true])
            ->willReturn($url);

        $this->assertEquals($url, $this->thisMock->getGridUrl());
    }
}
