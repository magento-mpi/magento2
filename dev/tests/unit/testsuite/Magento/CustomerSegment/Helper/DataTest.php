<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_CustomerSegment_Helper_Data
     */
    private $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_storeConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCollection;

    protected function setUp()
    {
        $translate = function (array $args) {
            return reset($args);
        };
        $translator = $this->getMock('Magento_Core_Model_Translate', array('translate'), array(), '', false);
        $translator->expects($this->any())->method('translate')->will($this->returnCallback($translate));
        $this->_storeConfig = $this->getMock('Magento_Core_Model_Store_Config', array('getConfig'), array(), '', false);
        $this->_segmentCollection = $this->getMock(
            'Magento_CustomerSegment_Model_Resource_Segment_Collection', array('toOptionArray'), array(), '', false
        );
        $helperContext = $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false);
        $helperContext->expects($this->any())->method('getTranslator')->will($this->returnValue($translator));
        $this->_helper = new Magento_CustomerSegment_Helper_Data(
            $helperContext,
            $this->_storeConfig,
            $this->_segmentCollection
        );
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_storeConfig = null;
        $this->_segmentCollection = null;
    }

    /**
     * @param array $fixtureFormData
     * @param bool $expectedUseSegments
     * @param string $expectedSegmentNote
     * @dataProvider addSegmentFieldsToFormDataProvider
     */
    public function testAddSegmentFieldsToForm(array $fixtureFormData, $expectedUseSegments, $expectedSegmentNote)
    {
        $this->markTestSkipped('Temporary skip by sout team');
        $this->_storeConfig
            ->expects($this->once())
            ->method('getConfig')
            ->with(Magento_CustomerSegment_Helper_Data::XML_PATH_CUSTOMER_SEGMENT_ENABLER)
            ->will($this->returnValue('1'))
        ;

        $this->_segmentCollection
            ->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(array(10 => 'Devs', 20 => 'QAs')))
        ;

        $form = new Magento_Data_Form(array('html_id_prefix' => 'pfx_'));
        $data = new Magento_Object($fixtureFormData);
        $dependencies = $this->getMock(
            'Magento_Backend_Block_Widget_Form_Element_Dependence',
            array('addFieldMap', 'addFieldDependence'),
            array(), '', false
        );

        $fieldset = new Magento_Data_Form_Element_Fieldset(array('advancedSection' => 'Additional Settings'));
        $fieldset->setId('base_fieldset');
        $form->addElement($fieldset);

        $dependencies
            ->expects($this->at(0))
            ->method('addFieldMap')
            ->with('pfx_use_customer_segment', 'use_customer_segment')
            ->will($this->returnSelf())
        ;
        $dependencies
            ->expects($this->at(1))
            ->method('addFieldMap')
            ->with('pfx_customer_segment_ids', 'customer_segment_ids')
            ->will($this->returnSelf())
        ;
        $dependencies
            ->expects($this->once())
            ->method('addFieldDependence')
            ->with('customer_segment_ids', 'use_customer_segment', '1')
            ->will($this->returnSelf())
        ;

        $this->_helper->addSegmentFieldsToForm($form, $data, $dependencies);

        $this->assertEquals($expectedUseSegments, $data->getData('use_customer_segment'));

        /** @var Magento_Data_Form_Element_Select $useSegmentElement */
        $useSegmentElement = $form->getElement('use_customer_segment');
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $useSegmentElement);
        $this->assertEquals('use_customer_segment', $useSegmentElement->getData('name'));
        $this->assertEquals('Customer Segments', $useSegmentElement->getData('label'));
        $this->assertEquals(array('0' => 'All', '1' => 'Specified'), $useSegmentElement->getData('options'));
        $this->assertEquals($expectedSegmentNote, $useSegmentElement->getData('note'));

        /** @var Magento_Data_Form_Element_Multiselect $segmentIdsElement */
        $segmentIdsElement = $form->getElement('customer_segment_ids');
        $this->assertInstanceOf('Magento_Data_Form_Element_Multiselect', $segmentIdsElement);
        $this->assertEquals('customer_segment_ids', $segmentIdsElement->getData('name'));
        $this->assertEquals(array(10 => 'Devs', 20 => 'QAs'), $segmentIdsElement->getData('values'));
        $this->assertTrue($segmentIdsElement->getData('required'));
        $this->assertTrue($segmentIdsElement->getData('can_be_empty'));
    }

    public function addSegmentFieldsToFormDataProvider()
    {
        return array(
            'all segments' => array(
                array(), false, 'Applies to All of the Specified Customer Segments'
            ),
            'specific segments' => array(
                array('customer_segment_ids' => array(123, 456)), true, 'Apply to the Selected Customer Segments'
            ),
        );
    }

    public function testAddSegmentFieldsToFormDisabled()
    {
        $this->_storeConfig
            ->expects($this->once())
            ->method('getConfig')
            ->with(Magento_CustomerSegment_Helper_Data::XML_PATH_CUSTOMER_SEGMENT_ENABLER)
            ->will($this->returnValue('0'))
        ;

        $this->_segmentCollection->expects($this->never())->method('toOptionArray');

        $form = new Magento_Data_Form(array('html_id_prefix' => 'pfx_'));
        $data = new Magento_Object();
        $dependencies = $this->getMock(
            'Magento_Backend_Block_Widget_Form_Element_Dependence',
            array('addFieldMap', 'addFieldDependence'),
            array(), '', false
        );

        $dependencies->expects($this->never())->method('addFieldMap');
        $dependencies->expects($this->never())->method('addFieldDependence');

        $this->_helper->addSegmentFieldsToForm($form, $data, $dependencies);

        $this->assertNull($data->getData('use_customer_segment'));
        $this->assertNull($form->getElement('use_customer_segment'));
        $this->assertNull($form->getElement('customer_segment_ids'));
    }
}
