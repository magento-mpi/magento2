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
namespace Magento\Backend\Block\System\Config\Form;

class FieldsetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Config\Form\Fieldset
     */
    protected $_object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_elementMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelMock;

    /**
     * @var array
     */
    protected $_testData;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_testHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            array(),
            array(),
            '',
            false,
            false
        );
        $this->_urlModelMock = $this->getMock('Magento\Backend\Model\Url', array(), array(), '', false, false);
        $this->_layoutMock = $this->getMock('Magento\View\Layout', array(), array(), '', false, false);
        $groupMock = $this->getMock(
            'Magento\Backend\Model\Config\Structure\Element\Group',
            array(),
            array(),
            '',
            false
        );
        $groupMock->expects($this->once())->method('getFieldsetCss')->will($this->returnValue('test_fieldset_css'));

        $this->_helperMock = $this->getMock('Magento\View\Helper\Js', array(), array(), '', false, false);

        $data = array(
            'request' => $this->_requestMock,
            'urlBuilder' => $this->_urlModelMock,
            'layout' => $this->_layoutMock,
            'jsHelper' => $this->_helperMock,
            'data' => array('group' => $groupMock)
        );
        $this->_testHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_object = $this->_testHelper->getObject('Magento\Backend\Block\System\Config\Form\Fieldset', $data);

        $this->_testData = array(
            'htmlId' => 'test_field_id',
            'name' => 'test_name',
            'label' => 'test_label',
            'elementHTML' => 'test_html',
            'legend' => 'test_legend',
            'comment' => 'test_comment'
        );

        $this->_elementMock = $this->getMock(
            'Magento\Data\Form\Element\Text',
            array('getHtmlId', 'getName', 'getExpanded', 'getElements', 'getLegend', 'getComment'),
            array(),
            '',
            false,
            false,
            true
        );

        $this->_elementMock->expects(
            $this->any()
        )->method(
            'getHtmlId'
        )->will(
            $this->returnValue($this->_testData['htmlId'])
        );
        $this->_elementMock->expects(
            $this->any()
        )->method(
            'getName'
        )->will(
            $this->returnValue($this->_testData['name'])
        );
        $this->_elementMock->expects($this->any())->method('getExpanded')->will($this->returnValue(true));
        $this->_elementMock->expects(
            $this->any()
        )->method(
            'getLegend'
        )->will(
            $this->returnValue($this->_testData['legend'])
        );
        $this->_elementMock->expects(
            $this->any()
        )->method(
            'getComment'
        )->will(
            $this->returnValue($this->_testData['comment'])
        );
    }

    public function testRenderWithoutStoredElements()
    {
        $collection = $this->_testHelper->getObject('Magento\Data\Form\Element\Collection');
        $this->_elementMock->expects($this->any())->method('getElements')->will($this->returnValue($collection));
        $actualHtml = $this->_object->render($this->_elementMock);
        $this->assertContains($this->_testData['htmlId'], $actualHtml);
        $this->assertContains($this->_testData['legend'], $actualHtml);
        $this->assertContains($this->_testData['comment'], $actualHtml);
    }

    public function testRenderWithStoredElements()
    {
        $this->_helperMock->expects($this->any())->method('getScript')->will($this->returnArgument(0));

        $fieldMock = $this->getMock(
            'Magento\Data\Form\Element\Text',
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

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $factoryColl = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array(), array(), '', false);
        $formMock = $this->getMock('Magento\Data\Form\AbstractForm', array(), array($factory, $factoryColl));
        $collection = $helper->getObject('Magento\Data\Form\Element\Collection', array('container' => $formMock));
        $collection->add($fieldMock);
        $this->_elementMock->expects($this->any())->method('getElements')->will($this->returnValue($collection));

        $actual = $this->_object->render($this->_elementMock);

        $this->assertContains('test_field_toHTML', $actual);

        $expected = '<div id="row_test_field_id_comment" class="system-tooltip-box"' .
            ' style="display:none;">test_field_tootip</div>';
        $this->assertContains($expected, $actual);
    }
}
