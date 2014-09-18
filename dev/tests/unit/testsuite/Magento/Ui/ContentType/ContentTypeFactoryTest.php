<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType;

class ContentTypeFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContentTypeFactory
     */
    protected $contentTypeFactory;

    /**
     * @param $type
     * @param @expected
     * @dataProvider getDataProvider
     */
    public function testGet($type, $contentRender, $expected)
    {
        $objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager',
            ['get', 'create', 'configure'],
            [],
            '',
            false
        );
        $this->contentTypeFactory = new ContentTypeFactory($objectManagerMock);
        $objectManagerMock->expects($this->once())->method('get')->with($expected)->willReturn($contentRender);
        $this->assertInstanceOf($expected, $this->contentTypeFactory->get($type));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetException()
    {
        $objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager',
            ['get', 'create', 'configure'],
            [],
            '',
            false
        );
        $this->contentTypeFactory = new ContentTypeFactory($objectManagerMock);
        $objectManagerMock->expects($this->once())->method('get')->willReturnSelf();
        $this->contentTypeFactory->get('test_exception');
    }

    public function getDataProvider()
    {
        $htmlMock = $this->getMock('Magento\Ui\ContentType\Html', [], [], '', false);
        $jsonMock = $this->getMock('Magento\Ui\ContentType\Json', [], [], '', false);
        $xmlMock = $this->getMock('Magento\Ui\ContentType\Xml', [], [], '', false);
        return [
            ['html', $htmlMock, 'Magento\Ui\ContentType\Html'],
            ['json', $jsonMock, 'Magento\Ui\ContentType\Json'],
            ['xml', $xmlMock, 'Magento\Ui\ContentType\Xml'],
            ['default', $htmlMock, 'Magento\Ui\ContentType\Html']
        ];
    }
}
