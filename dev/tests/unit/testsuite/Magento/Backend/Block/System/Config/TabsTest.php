<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_System_Config_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Tabs
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilderMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $this->_requestMock->expects($this->any())->method('getParam')->with('section')
            ->will($this->returnValue('currentSectionId'));
        $this->_structureMock = $this->getMock('Magento\Backend\Model\Config\Structure', array(), array(), '', false);
        $this->_structureMock->expects($this->once())->method('getTabs')->will($this->returnValue(array()));
        $this->_urlBuilderMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false);
        $layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false);
        $helperMock = $this->getMock('Magento\Core\Helper\Data', array('addPageHelpUrl'), array(), '', false);
        $helperMock->expects($this->once())->method('addPageHelpUrl')->with('currentSectionId/');
        $layoutMock->expects($this->any())->method('helper')->will($this->returnValue($helperMock));

        $data = array(
            'configStructure' => $this->_structureMock,
            'request' => $this->_requestMock,
            'urlBuilder' => $this->_urlBuilderMock,
            'layout' => $layoutMock,
        );
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_object = $helper->getObject('\Magento\Backend\Block\System\Config\Tabs', $data);
    }

    protected function tearDown()
    {
        unset($this->_object);
        unset($this->_requestMock);
        unset($this->_structureMock);
        unset($this->_urlBuilderMock);
    }

    public function testGetSectionUrl()
    {
        $this->_urlBuilderMock->expects($this->once())->method('getUrl')
            ->with('*/*/*', array('_current' => true, 'section' => 'testSectionId'))
            ->will($this->returnValue('testSectionUrl'));
        $sectionMock = $this->getMock(
            '\Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())->method('getId')->will($this->returnValue('testSectionId'));
        $this->assertEquals('testSectionUrl', $this->_object->getSectionUrl($sectionMock));
    }

    public function testIsSectionActiveReturnsTrueForActiveSection()
    {
        $sectionMock = $this->getMock(
            '\Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())->method('getId')->will($this->returnValue('currentSectionId'));
        $this->assertTrue($this->_object->isSectionActive($sectionMock));
    }

    public function testIsSectionActiveReturnsFalseForNonActiveSection()
    {
        $sectionMock = $this->getMock(
            '\Magento\Backend\Model\Config\Structure\Element\Section', array(), array(), '', false
        );
        $sectionMock->expects($this->once())->method('getId')->will($this->returnValue('nonCurrentSectionId'));
        $this->assertFalse($this->_object->isSectionActive($sectionMock));
    }
}
