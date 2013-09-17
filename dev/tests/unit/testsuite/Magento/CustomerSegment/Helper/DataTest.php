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
     * @dataProvider addSegmentFieldsToFormDataProvider
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAddSegmentFieldsToForm(array $fixtureFormData)
    {
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

        $fieldset = $this->getMock('Magento_Data_Form_Element_Fieldset', array('addField'), array(), '', false);
        $fieldset->expects($this->at(0))
            ->method('addField')
            ->with($this->logicalOr(
                $this->equalTo('use_customer_segment'),
                $this->equalTo('select')
            ));
        $fieldset->expects($this->at(1))
            ->method('addField')
            ->with($this->logicalOr(
                $this->equalTo('customer_segment_ids'),
                $this->equalTo('multiselect')
            ));

        $form = $this->getMock('Magento_Data_Form', array('getElement', 'getHtmlIdPrefix'), array(), '', false);
        $form->expects($this->once())
            ->method('getElement')
            ->with($this->equalTo('base_fieldset'))
            ->will($this->returnValue($fieldset));
        $form->expects($this->once())
            ->method('getHtmlIdPrefix')
            ->will($this->returnValue('pfx_'));

        $data = new Magento_Object($fixtureFormData);

        $dependencies = $this->getMock(
            'Magento_Backend_Block_Widget_Form_Element_Dependence',
            array('addFieldMap', 'addFieldDependence'),
            array(), '', false
        );
        $dependencies
            ->expects($this->at(0))
            ->method('addFieldMap')
            ->with('pfx_use_customer_segment', 'use_customer_segment')
            ->will($this->returnSelf());
        $dependencies
            ->expects($this->at(1))
            ->method('addFieldMap')
            ->with('pfx_customer_segment_ids', 'customer_segment_ids')
            ->will($this->returnSelf());
        $dependencies
            ->expects($this->once())
            ->method('addFieldDependence')
            ->with('customer_segment_ids', 'use_customer_segment', '1')
            ->will($this->returnSelf());

        $this->_helper->addSegmentFieldsToForm($form, $data, $dependencies);
    }

    public function addSegmentFieldsToFormDataProvider()
    {
        return array(
            'all segments' => array(array()),
            'specific segments' => array(array('customer_segment_ids' => array(123, 456))),
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

        $factory = $this->getMock('Magento_Data_Form_Element_Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento_Data_Form_Element_CollectionFactory', array('create'),
            array(), '', false);
        $form = new Magento_Data_Form($factory, $collectionFactory, array('html_id_prefix' => 'pfx_'));
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
