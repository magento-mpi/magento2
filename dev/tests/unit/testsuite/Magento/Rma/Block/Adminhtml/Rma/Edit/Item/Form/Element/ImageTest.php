<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element;

/**
 * Test class for Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element\Image
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
            ->disableOriginalConstructor()
            ->getMock();
        $this->image = $objectManager->getObject(
            'Magento\Customer\Block\Adminhtml\Form\Element\Image',
            ['adminhtmlData' => $this->backendHelperMock]
        );
    }

    public function testGetHiddenInput()
    {
        $name = 'test_name';
        $formMock = $this->getMockBuilder('Magento\Framework\Data\Form')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->image->setForm($formMock);
        $this->image->setName($name);

        $this->assertContains($name, $this->image->getElementHtml());
    }
}
