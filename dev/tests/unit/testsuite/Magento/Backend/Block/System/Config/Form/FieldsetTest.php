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

class Magento_Backend_Block_System_Config_Form_FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Form\Fieldset
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

    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_testHelper;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false,
            false);
        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false);
        $this->_layoutMock = $this->getMock('Magento\Core\Model\Layout', array(), array(), '', false, false);
        $groupMock = $this->getMock('Magento\Backend\Model\Config\Structure\Element\Group', array(), array(), '',
            false);
        $groupMock->expects($this->once())->method('getFieldsetCss')->will($this->returnValue('test_fieldset_css'));

        $data = array(
            'request' => $this->_requestMock,
            'urlBuilder' => $this->_urlModelMock,
            'layout' => $this->_layoutMock,
            'data' => array(
                'group' => $groupMock
            )
        );
        $this->_testHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_object = $this->_testHelper->getObject('\Magento\Backend\Block\System\Config\Form\Fieldset', $data);

        $this->_testData = array(
            'htmlId' => 'test_field_id',
            'name' => 'test_name',
            'label' => 'test_label',
            'elementHTML' => 'test_html',
            'legend' => 'test_legend',
            'comment' => 'test_comment',
        );

        $this->_elementMock = $this->getMock('Magento\Data\Form\Element\Text',
            array('getHtmlId' , 'getName', 'getExpanded', 'getElements', 'getLegend', 'getComment'),
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
        $helperMock = $this->getMock('Magento\Core\Helper\Js', array(), array(), '', false, false);

        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Magento\Core\Helper\Js')->will($this->returnValue($helperMock));

        $collection = $this->_testHelper->getObject('\Magento\Data\Form\Element\Collection');
        $this->_elementMock->expects($this->any())->method('getElements')->will($this->returnValue($collection));
        $actualHtml = $this->_object->render($this->_elementMock);
        $this->assertContains($this->_testData['htmlId'], $actualHtml);
        $this->assertContains($this->_testData['legend'], $actualHtml);
        $this->assertContains($this->_testData['comment'], $actualHtml);
    }

    public function testRenderWithStoredElements()
    {
        $helperMock = $this->getMock('Magento\Core\Helper\Js', array(), array(), '', false, false);
        $helperMock->expects($this->any())->method('getScript')->will($this->returnArgument(0));

        $this->_layoutMock->expects($this->any())->method('helper')
            ->with('Magento\Core\Helper\Js')->will($this->returnValue($helperMock));

        $fieldMock = $this->getMock('Magento\Data\Form\Element\Text',
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

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $collection = $helper->getObject('\Magento\Data\Form\Element\Collection', array(
            'container' => $this->getMock('Magento\Data\Form\AbstractForm')
        ));
        $collection->add($fieldMock);
        $this->_elementMock->expects($this->any())->method('getElements')
            ->will($this->returnValue($collection)
        );

        $actual = $this->_object->render($this->_elementMock);

        $this->assertContains('test_field_toHTML', $actual);

        $expected = '<div id="row_test_field_id_comment" class="system-tooltip-box"'
            .' style="display:none;">test_field_tootip</div>';
        $this->assertContains($expected, $actual);
    }
}
