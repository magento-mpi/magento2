<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\AbstractElement
 */
namespace Magento\Framework\Data\Form\Element;

class AbstractElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Form\Element\AbstractElement|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactoryMock;

    /**
     * @var \Magento\Framework\Escaper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_escaperMock;

    protected function setUp()
    {
        $this->_factoryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false
        );
        $this->_collectionFactoryMock = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory', array(), array(), '', false
        );
        $this->_escaperMock = $this->getMock('Magento\Framework\Escaper', array(), array(), '', false);

        $this->_model = $this->getMockForAbstractClass('Magento\Framework\Data\Form\Element\AbstractElement', array(
            $this->_factoryMock,
            $this->_collectionFactoryMock,
            $this->_escaperMock
        ));
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::addElement()
     */
    public function testAddElement()
    {
        $elementId = 11;
        $elementMock = $this->getMockForAbstractClass(
            'Magento\Framework\Data\Form\Element\AbstractElement', array(), '', false, true, true, array('getId')
        );
        $elementMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($elementId));

        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm',
            array('checkElementId', 'addElementToCollection'),
            array(),
            '',
            false
        );
        $formMock->expects($this->once())
            ->method('checkElementId')
            ->with($elementId);
        $formMock->expects($this->once())
            ->method('addElementToCollection')
            ->with($elementMock);

        $collectionMock = $this->getMock('Magento\Framework\Data\Form\Element\Collection', array(), array(), '', false);

        $this->_collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $this->_model->setForm($formMock);
        $this->_model->addElement($elementMock);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlId()
     */
    public function testGetHtmlId()
    {
        $htmlIdPrefix = '--';
        $htmlIdSuffix = ']]';
        $htmlId = 'some_id';

        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm', array('getHtmlIdPrefix', 'getHtmlIdSuffix'), array(), '', false
        );
        $formMock->expects($this->any())
            ->method('getHtmlIdPrefix')
            ->will($this->returnValue($htmlIdPrefix));
        $formMock->expects($this->any())
            ->method('getHtmlIdSuffix')
            ->will($this->returnValue($htmlIdSuffix));

        $this->_model->setId($htmlId);
        $this->_model->setForm($formMock);
        $this->assertEquals($htmlIdPrefix . $htmlId . $htmlIdSuffix, $this->_model->getHtmlId());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getName()
     */
    public function testGetNameWithoutSuffix()
    {
        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm',
            array('getFieldNameSuffix', 'addSuffixToName'),
            array(),
            '',
            false
        );
        $formMock->expects($this->any())
            ->method('getFieldNameSuffix')
            ->will($this->returnValue(null));
        $formMock->expects($this->never())
            ->method('addSuffixToName');

        $this->_model->setForm($formMock);
        $this->_model->getName();
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getName()
     */
    public function testGetNameWithSuffix()
    {
        $returnValue = 'some_value';

        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm',
            array('getFieldNameSuffix', 'addSuffixToName'),
            array(),
            '',
            false
        );
        $formMock->expects($this->once())
            ->method('getFieldNameSuffix')
            ->will($this->returnValue(true));
        $formMock->expects($this->once())
            ->method('addSuffixToName')
            ->will($this->returnValue($returnValue));

        $this->_model->setForm($formMock);

        $this->assertEquals($returnValue, $this->_model->getName());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::removeField()
     */
    public function testRemoveField()
    {
        $elementId = 'element_id';

        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm', array('removeField'), array(), '', false
        );
        $formMock->expects($this->once())
            ->method('removeField')
            ->with($elementId);

        $collectionMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\Collection', array('remove'), array(), '', false
        );
        $collectionMock->expects($this->once())
            ->method('remove')
            ->with($elementId);

        $this->_collectionFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $this->_model->setForm($formMock);
        $this->_model->removeField($elementId);
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlAttributes()
     */
    public function testGetHtmlAttributes()
    {
        $htmlAttributes = array(
            'type',
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'disabled',
            'readonly',
            'tabindex',
            'placeholder',
            'data-form-part'
        );
        $this->assertEquals($htmlAttributes, $this->_model->getHtmlAttributes());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::addClass()
     */
    public function testAddClass()
    {
        $oldClass = 'old_class';
        $newClass = 'new_class';
        $this->_model->addClass($oldClass);
        $this->_model->addClass($newClass);

        $this->assertEquals(' ' . $oldClass . ' ' . $newClass, $this->_model->getClass());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::removeClass()
     */
    public function testRemoveClass()
    {
        $oldClass = 'old_class';
        $newClass = 'new_class';
        $oneMoreClass = 'some_class';
        $this->_model->addClass($oldClass);
        $this->_model->addClass($oneMoreClass);
        $this->_model->addClass($newClass);

        $this->_model->removeClass($oneMoreClass);

        $this->assertEquals(' ' . $oldClass . ' ' . $newClass, $this->_model->getClass());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getEscapedValue()
     */
    public function testGetEscapedValueWithoutFilter()
    {
        $this->_model->setValue('<a href="#hash_tag">my \'quoted\' string</a>');
        $this->assertEquals(
            '&lt;a href=&quot;#hash_tag&quot;&gt;my \'quoted\' string&lt;/a&gt;', $this->_model->getEscapedValue()
        );
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getEscapedValue()
     */
    public function testGetEscapedValueWithFilter()
    {
        $value = '<a href="#hash_tag">my \'quoted\' string</a>';
        $expectedValue = '&lt;a href=&quot;#hash_tag&quot;&gt;my \'quoted\' string&lt;/a&gt;';

        $filterMock = $this->getMock('Magento\Framework\Object', array('filter'), array(), '', false);
        $filterMock->expects($this->once())
            ->method('filter')
            ->with($value)
            ->will($this->returnArgument(0));

        $this->_model->setValueFilter($filterMock);
        $this->_model->setValue($value);
        $this->assertEquals($expectedValue, $this->_model->getEscapedValue());
    }

    /**
     * @param array $initialData
     * @param string $expectedValue
     * @dataProvider getElementHtmlDataProvider
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
     */
    public function testGetElementHtml(array $initialData, $expectedValue)
    {
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );

        $this->_model->setData($initialData);
        $this->assertEquals($expectedValue, $this->_model->getElementHtml());
    }

    /**
     * @param array $initialData
     * @param string $expectedValue
     * @dataProvider getLabelHtmlDataProvider
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getLabelHtml()
     */
    public function testGetLabelHtml(array $initialData, $expectedValue)
    {
        $idSuffix = isset($initialData['id_suffix']) ? $initialData['id_suffix'] : null;
        $this->_model->setData($initialData);
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );
        $this->assertEquals($expectedValue, $this->_model->getLabelHtml($idSuffix));
    }

    /**
     * @param array $initialData
     * @param string $expectedValue
     * @dataProvider testGetDefaultHtmlDataProvider
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getDefaultHtml()
     */
    public function testGetDefaultHtml(array $initialData, $expectedValue)
    {
        $this->_model->setData($initialData);
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );
        $this->assertEquals($expectedValue, $this->_model->getDefaultHtml());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtml()
     */
    public function testGetHtmlWithoutRenderer()
    {
        $this->_model->setRequired(true);
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );
        $expectedHtml = '<span class="field-row">' . "\n"
            . '<input id="" name=""  data-ui-id="form-element-" value="" class=" required-entry"/></span>' . "\n";

        $this->assertEquals($expectedHtml, $this->_model->getHtml());
        $this->assertEquals(' required-entry', $this->_model->getClass());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtml()
     */
    public function testGetHtmlWithRenderer()
    {
        $this->_model->setRequired(true);

        $expectedHtml = 'some-html';

        $rendererMock = $this->getMockForAbstractClass(
            'Magento\Framework\Data\Form\Element\Renderer\RendererInterface'
        );
        $rendererMock->expects($this->once())
            ->method('render')
            ->with($this->_model)
            ->will($this->returnValue($expectedHtml));
        $this->_model->setRenderer($rendererMock);

        $this->assertEquals($expectedHtml, $this->_model->getHtml());
        $this->assertEquals(' required-entry', $this->_model->getClass());
    }

    /**
     * @param array $initialData
     * @param string $expectedValue
     * @dataProvider serializeDataProvider
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::serialize()
     */
    public function testSerialize(array $initialData, $expectedValue)
    {
        $attributes = array();
        if (isset($initialData['attributes'])) {
            $attributes = $initialData['attributes'];
            unset($initialData['attributes']);
        }
        $this->_model->setData($initialData);
        $this->assertEquals($expectedValue, $this->_model->serialize($attributes));
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlContainerId()
     */
    public function testGetHtmlContainerIdWithoutId()
    {
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );
        $this->assertEquals('', $this->_model->getHtmlContainerId());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlContainerId()
     */
    public function testGetHtmlContainerIdWithContainerId()
    {
        $containerId = 'some-id';
        $this->_model->setContainerId($containerId);
        $this->_model->setForm(
            $this->getMock('Magento\Framework\Data\Form\AbstractForm', array(), array(), '', false)
        );
        $this->assertEquals($containerId, $this->_model->getHtmlContainerId());
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::getHtmlContainerId()
     */
    public function testGetHtmlContainerIdWithFieldContainerIdPrefix()
    {
        $id = 'id';
        $prefix = 'prefix_';
        $formMock = $this->getMock(
            'Magento\Framework\Data\Form\AbstractForm', array('getFieldContainerIdPrefix'), array(), '', false
        );
        $formMock->expects($this->once())
            ->method('getFieldContainerIdPrefix')
            ->will($this->returnValue($prefix));

        $this->_model->setId($id);
        $this->_model->setForm($formMock);
        $this->assertEquals($prefix . $id, $this->_model->getHtmlContainerId());
    }

    /**
     * @param array $initialData
     * @param string $expectedValue
     * @dataProvider addElementValuesDataProvider
     * @covers \Magento\Framework\Data\Form\Element\AbstractElement::addElementValues()
     */
    public function testAddElementValues(array $initialData, $expectedValue)
    {
        $this->_escaperMock->expects($this->any())
            ->method('escapeHtml')
            ->will($this->returnArgument(0));
        $this->_model->setValues($initialData['initial_values']);
        $this->_model->addElementValues($initialData['add_values'], $initialData['overwrite']);

        $this->assertEquals($expectedValue, $this->_model->getValues());
    }

    /**
     * @return array
     */
    public function addElementValuesDataProvider()
    {
        return array(
            array(
                array(
                    'initial_values' => array(
                        'key_1' => 'value_1',
                        'key_2' => 'value_2',
                        'key_3' => 'value_3'
                    ),
                    'add_values' => array(
                        'key_1' => 'value_4',
                        'key_2' => 'value_5',
                        'key_3' => 'value_6',
                        'key_4' => 'value_7'
                    ),
                    'overwrite' => false
                ),
                array(
                    'key_1' => 'value_1',
                    'key_2' => 'value_2',
                    'key_3' => 'value_3',
                    'key_4' => 'value_7'
                )
            ),
            array(
                array(
                    'initial_values' => array(
                        'key_1' => 'value_1',
                        'key_2' => 'value_2',
                        'key_3' => 'value_3'
                    ),
                    'add_values' => array(
                        'key_1' => 'value_4',
                        'key_2' => 'value_5',
                        'key_3' => 'value_6',
                        'key_4' => 'value_7'
                    ),
                    'overwrite' => true
                ),
                array(
                    'key_1' => 'value_4',
                    'key_2' => 'value_5',
                    'key_3' => 'value_6',
                    'key_4' => 'value_7'
                )
            )
        );
    }

    /**
     * @return array
     */
    public function serializeDataProvider()
    {
        return array(
            array(
                array(),
                ''
            ),
            array(
                array(
                    'attributes' => array('disabled'),
                    'disabled' => true
                ),
                'disabled="disabled"'
            ),
            array(
                array(
                    'attributes' => array('checked'),
                    'checked' => true
                ),
                'checked="checked"'
            ),
            array(
                array(
                    'data-locked' => 1,
                    'attributes' => array('attribute_1')
                ),
                'data-locked="1"'
            )
        );
    }

    /**
     * @return array
     */
    public function testGetDefaultHtmlDataProvider()
    {
        return array(
            array(
                array(),
                '<span class="field-row">' . "\n"
                    . '<input id="" name=""  data-ui-id="form-element-" value="" /></span>' . "\n"
            ),
            array(
                array('default_html' => 'some default html'),
                'some default html'
            ),
            array(
                array(
                    'label' => 'some label',
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value',
                ),
                '<span class="field-row">' . "\n"
                    . '<label class="label" for="html-id" data-ui-id="form-element-some-namelabel">'
                    . '<span>some label</span></label>' . "\n"
                    . '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />'
                    . '</span>' . "\n"
            ),
            array(
                array(
                    'label' => 'some label',
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value',
                    'no_span' => true
                ),
                '<label class="label" for="html-id" data-ui-id="form-element-some-namelabel">'
                    . '<span>some label</span></label>' . "\n"
                    . '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />'
            ),
        );
    }

    /**
     * @return array
     */
    public function getLabelHtmlDataProvider()
    {
        return array(
            array(
                array(),
                ''
            ),
            array(
                array(
                    'id_suffix' => 'suffix'
                ),
                ''
            ),
            array(
                array(
                    'label' => 'some-label',
                    'html_id' => 'some-html-id'
                ),
                '<label class="label" for="some-html-id" data-ui-id="form-element-label">'
                    . '<span>some-label</span></label>' . "\n"
            ),
            array(
                array(
                    'id_suffix' => 'suffix',
                    'label' => 'some-label',
                    'html_id' => 'some-html-id'
                ),
                '<label class="label" for="some-html-idsuffix" data-ui-id="form-element-label">'
                    . '<span>some-label</span></label>' . "\n"
            ),
        );
    }

    /**
     * @return array
     */
    public function getElementHtmlDataProvider()
    {
        return array(
            array(
                array(),
                '<input id="" name=""  data-ui-id="form-element-" value="" />'
            ),
            array(
                array(
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value'
                ),
                '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />'
            ),
            array(
                array(
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value',
                    'before_element_html' => 'some-html'
                ),
                '<label class="addbefore" for="html-id">some-html</label>'
                    . '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />'
            ),
            array(
                array(
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value',
                    'after_element_js' => 'some-js'
                ),
                '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />some-js'
            ),
            array(
                array(
                    'html_id' => 'html-id',
                    'name' => 'some-name',
                    'value' => 'some-value',
                    'after_element_html' => 'some-html'
                ),
                '<input id="html-id" name="some-name"  data-ui-id="form-element-some-name" value="some-value" />'
                    . '<label class="addafter" for="html-id">some-html</label>'
            )
        );
    }
}
