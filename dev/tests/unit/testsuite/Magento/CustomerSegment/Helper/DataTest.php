<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Helper\Data
     */
    private $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_scopeConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_formKeyMock;

    protected function setUp()
    {
        $this->_formKeyMock = $this->getMock('Magento\Framework\Data\Form\FormKey', array(), array(), '', false);
        $this->_scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_segmentCollection = $this->getMock(
            'Magento\CustomerSegment\Model\Resource\Segment\Collection',
            array('toOptionArray'),
            array(),
            '',
            false
        );
        $helperContext = $this->getMock('Magento\Framework\App\Helper\Context', array(), array(), '', false);
        $this->_helper = new \Magento\CustomerSegment\Helper\Data(
            $helperContext,
            $this->_scopeConfig,
            $this->_segmentCollection
        );
    }

    protected function tearDown()
    {
        $this->_helper = null;
        $this->_scopeConfig = null;
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
        $this->_scopeConfig->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            \Magento\CustomerSegment\Helper\Data::XML_PATH_CUSTOMER_SEGMENT_ENABLER
        )->will(
            $this->returnValue('1')
        );

        $this->_segmentCollection->expects(
            $this->once()
        )->method(
            'toOptionArray'
        )->will(
            $this->returnValue(array(10 => 'Devs', 20 => 'QAs'))
        );

        $fieldset = $this->getMock(
            'Magento\Framework\Data\Form\Element\Fieldset',
            array('addField'),
            array(),
            '',
            false
        );
        $fieldset->expects(
            $this->at(0)
        )->method(
            'addField'
        )->with(
            $this->logicalOr($this->equalTo('use_customer_segment'), $this->equalTo('select'))
        );
        $fieldset->expects(
            $this->at(1)
        )->method(
            'addField'
        )->with(
            $this->logicalOr($this->equalTo('customer_segment_ids'), $this->equalTo('multiselect'))
        );

        $form = $this->getMock(
            'Magento\Framework\Data\Form',
            array('getElement', 'getHtmlIdPrefix'),
            array(),
            '',
            false
        );
        $form->expects(
            $this->once()
        )->method(
            'getElement'
        )->with(
            $this->equalTo('base_fieldset')
        )->will(
            $this->returnValue($fieldset)
        );
        $form->expects($this->once())->method('getHtmlIdPrefix')->will($this->returnValue('pfx_'));

        $data = new \Magento\Framework\Object($fixtureFormData);

        $dependencies = $this->getMock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            array('addFieldMap', 'addFieldDependence'),
            array(),
            '',
            false
        );
        $dependencies->expects(
            $this->at(0)
        )->method(
            'addFieldMap'
        )->with(
            'pfx_use_customer_segment',
            'use_customer_segment'
        )->will(
            $this->returnSelf()
        );
        $dependencies->expects(
            $this->at(1)
        )->method(
            'addFieldMap'
        )->with(
            'pfx_customer_segment_ids',
            'customer_segment_ids'
        )->will(
            $this->returnSelf()
        );
        $dependencies->expects(
            $this->once()
        )->method(
            'addFieldDependence'
        )->with(
            'customer_segment_ids',
            'use_customer_segment',
            '1'
        )->will(
            $this->returnSelf()
        );

        $this->_helper->addSegmentFieldsToForm($form, $data, $dependencies);
    }

    public function addSegmentFieldsToFormDataProvider()
    {
        return array(
            'all segments' => array(array()),
            'specific segments' => array(array('customer_segment_ids' => array(123, 456)))
        );
    }

    public function testAddSegmentFieldsToFormDisabled()
    {
        $this->_scopeConfig->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            \Magento\CustomerSegment\Helper\Data::XML_PATH_CUSTOMER_SEGMENT_ENABLER
        )->will(
            $this->returnValue('0')
        );

        $this->_segmentCollection->expects($this->never())->method('toOptionArray');

        $factory = $this->getMock('Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $form = new \Magento\Framework\Data\Form(
            $factory,
            $collectionFactory,
            $this->_formKeyMock,
            array('html_id_prefix' => 'pfx_')
        );
        $data = new \Magento\Framework\Object();
        $dependencies = $this->getMock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            array('addFieldMap', 'addFieldDependence'),
            array(),
            '',
            false
        );

        $dependencies->expects($this->never())->method('addFieldMap');
        $dependencies->expects($this->never())->method('addFieldDependence');

        $this->_helper->addSegmentFieldsToForm($form, $data, $dependencies);

        $this->assertNull($data->getData('use_customer_segment'));
        $this->assertNull($form->getElement('use_customer_segment'));
        $this->assertNull($form->getElement('customer_segment_ids'));
    }
}
