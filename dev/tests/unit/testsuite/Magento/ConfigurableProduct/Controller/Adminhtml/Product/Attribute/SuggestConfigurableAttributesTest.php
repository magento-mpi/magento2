<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Attribute;

class SuggestConfigurableAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Controller\Adminhtml\Product\Attribute\SuggestConfigurableAttributes
     */
    protected $suggestAttributes;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeListMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false);
        $this->requestMock = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->helperMock = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->attributeListMock =
            $this->getMock('Magento\ConfigurableProduct\Model\SuggestedAttributeList', array(), array(), '', false);
        $this->suggestAttributes = $helper->getObject(
            'Magento\ConfigurableProduct\Controller\Adminhtml\Product\Attribute\SuggestConfigurableAttributes', array(
                'response' => $this->responseMock,
                'request' => $this->requestMock,
                'coreHelper' => $this->helperMock,
                'attributeList' => $this->attributeListMock
            )
        );
    }

    public function testIndexAction()
    {

        $this->requestMock
            ->expects($this->once())
            ->method('getParam')
            ->with('label_part')
            ->will($this->returnValue('attribute'));
        $this->attributeListMock
            ->expects($this->once())
            ->method('getSuggestedAttributes')
            ->with('attribute')
            ->will($this->returnValue('some_value_for_json'));
        $this->helperMock
            ->expects($this->once())
            ->method('jsonEncode')
            ->with('some_value_for_json')
            ->will($this->returnValue('body'));
        $this->responseMock->expects($this->once())->method('setBody')->with('body');
        $this->suggestAttributes->indexAction();

    }
}

