<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Block_System_Config_Form_FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Block_System_Config_Form_Fieldset
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_elementMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var array
     */
    protected $_testData;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;


    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false, false);
        $this->_urlModelMock = $this->getMock('Mage_Backend_Model_Url', array(), array(), '', false, false);
        $this->_layoutMock = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false, false);
        $groupMock = $this->getMock('Mage_Backend_Model_Config_Structure_Element_Group', array(), array(), '', false);
        $groupMock->expects($this->once())->method('getFieldsetCss')->will($this->returnValue('test_fieldset_css'));

        $data = array(
            'request' => $this->_requestMock,
            'urlBuilder' => $this->_urlModelMock,
            'layout' => $this->_layoutMock,
            'data' => array(
                'group' => $groupMock
            )
        );
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_object = $helper->getObject('Mage_Backend_Block_System_Config_Form_Fieldset', $data);

        $this->_testData = array(
            'htmlId' => 'test_field_id',
            'name' => 'test_name',
            'label' => 'test_label',
            'elementHTML' => 'test_html',
            'legend' => 'test_legend',
            'comment' => 'test_comment',
        );

        $this->_elementMock = $this->getMock('Varien_Data_Form_Element_Text',
            array('getHtmlId' , 'getName', 'getExpanded', 'getSortedElements', 'getLegend', 'getComment'),
            array(),
            '',
            false,
            false,
            true
        );

        $this->_elementMock->expects($this->any())->method('getHtmlId')
            ->will($this->returnValue($this->_testData['htmlId']));
        $this->_elementMock->expects($this->any())->method('getName')
            ->will($this->returnValue($this->_testData['name']));
        $this->_elementMock->expects($this->any())->method('getExpanded')
            ->will($this->returnValue(true));
        $this->_elementMock->expects($this->any())->method('getLegend')
            ->will($this->returnValue($this->_testData['legend']));
        $this->_elementMock->expects($this->any())->method('getComment')
            ->will($this->returnValue($this->_testData['comment']));
    }

    public function testRenderWithoutStoredElements()
    {
        $helperMock = $this->getMock('Mage_Core_Helper_Js', array(), array(), '', false, false);
        $helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Mage_Core_Helper_Js')->will($this->returnValue($helperMock));

        $this->_elementMock->expects($this->any())->method('getSortedElements')->will($this->returnValue(array()));
        $actualHtml = $this->_object->render($this->_elementMock);
        $this->assertContains($this->_testData['htmlId'], $actualHtml);
        $this->assertContains($this->_testData['legend'], $actualHtml);
        $this->assertContains($this->_testData['comment'], $actualHtml);
    }

    public function testRenderWithStoredElements()
    {
        $helperMock = $this->getMock('Mage_Core_Helper_Js', array(), array(), '', false, false);
        $helperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $helperMock->expects($this->any())->method('getScript')->will($this->returnArgument(0));

        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Mage_Core_Helper_Js')->will($this->returnValue($helperMock));

        $fieldMock = $this->getMock('Varien_Data_Form_Element_Text',
            array('getId', 'getTooltip', 'toHtml'),
            array(),
            '',
            false,
            false,
            true
        );

        $fieldMock->expects($this->any())->method('getId')->will($this->returnValue('test_field_id'));
        $fieldMock->expects($this->any())->method('getTooltip')->will($this->returnValue('test_field_tootip'));
        $fieldMock->expects($this->any())->method('toHtml')->will($this->returnValue('test_field_toHTML'));

        $this->_elementMock->expects($this->any())->method('getSortedElements')
            ->will($this->returnValue( array($fieldMock))
        );

        $actual = $this->_object->render($this->_elementMock);

        $this->assertContains('test_field_toHTML', $actual);

        $expected = '<div id="row_test_field_id_comment" class="system-tooltip-box"'
            .' style="display:none;">test_field_tootip</div>';
        $this->assertContains($expected, $actual);
    }
}
