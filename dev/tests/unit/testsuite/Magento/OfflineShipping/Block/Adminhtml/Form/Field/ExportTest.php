<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\OfflineShipping\Block\Adminhtml\Form\Field;

class ExportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Form\Field\Export
     */
    protected $_object;

    protected function setUp()
    {
        $backendUrl = $this->getMock('Magento\Backend\Model\UrlInterface', array(), array(), '', false, false);
        $backendUrl->expects($this->once())->method('getUrl')->with("*/*/exportTablerates", array('website' => 1));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $objectManager->getObject('Magento\OfflineShipping\Block\Adminhtml\Form\Field\Export', array(
            'backendUrl' => $backendUrl)
        );
    }

    public function testGetElementHtml()
    {
        $expected = 'some test data';

        $form = $this->getMock('Magento\Data\Form', array('getParent'), array(), '', false, false);
        $parentObjectMock = $this->getMock('Magento\Backend\Block\Template',
            array('getLayout'), array(), '', false, false
        );
        $layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false, false);

        $blockMock = $this->getMock('Magento\Backend\Block\Widget\Button', array(), array(), '', false, false);

        $requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false, false);
        $requestMock->expects($this->once())->method('getParam')->with('website')->will($this->returnValue(1));

        $mockData = $this->getMock('StdClass', array('toHtml'));
        $mockData->expects($this->once())->method('toHtml')->will($this->returnValue($expected));

        $blockMock->expects($this->once())->method('getRequest')->will($this->returnValue($requestMock));
        $blockMock->expects($this->any())->method('setData')->will($this->returnValue($mockData));


        $layoutMock->expects($this->once())->method('createBlock')->will($this->returnValue($blockMock));
        $parentObjectMock->expects($this->once())->method('getLayout')->will($this->returnValue($layoutMock));
        $form->expects($this->once())->method('getParent')->will($this->returnValue($parentObjectMock));

        $this->_object->setForm($form);
        $this->assertEquals($expected, $this->_object->getElementHtml());
    }
}
