<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\From\Element;

/**
 * Test class for \Magento\Customer\Block\Adminhtml\From\Element\Image
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Block\Adminhtml\Form\Element\Image
     */
    protected $image;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendHelperMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->backendHelperMock = $this->getMockBuilder('Magento\Backend\Helper\Data')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->image = $objectManager->getObject(
            'Magento\Customer\Block\Adminhtml\Form\Element\Image',
            ['adminhtmlData' => $this->backendHelperMock]
        );
    }

    public function testGetPreviewFile()
    {
        $value = 'image.jpg';
        $url = 'http://example.com/backend/customer/index/viewfile/' . $value;
        $formMock = $this->getMockBuilder('Magento\Framework\Data\Form')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->image->setForm($formMock);
        $this->image->setValue($value);

        $this->backendHelperMock->expects($this->once())
            ->method('urlEncode')
            ->with($value)
            ->will($this->returnArgument(0));
        $this->backendHelperMock->expects($this->once())
            ->method('getUrl')
            ->with('customer/index/viewfile', ['image' => $value])
            ->will($this->returnValue($url));

        $this->assertContains($url, $this->image->getElementHtml());
    }
}
