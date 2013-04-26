<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_BannerCustomerSegment_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_BannerCustomerSegment_Model_Observer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_resource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCustomer;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCollection;

    protected function setUp()
    {
        $this->_resource = $this->getMock(
            'Mage_Core_Model_Resource', array('getConnection', 'getTableName'), array(), '', false
        );
        $this->_resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));

        $this->_segmentCustomer = $this->getMock(
            'Enterprise_CustomerSegment_Model_Customer', array('getCurrentCustomerSegmentIds'), array(), '', false
        );
        $this->_segmentHelper = $this->getMock(
            'Enterprise_CustomerSegment_Helper_Data', array('isEnabled', 'addSegmentFieldsToForm'), array(), '', false
        );
        $this->_segmentCollection = $this->getMock(
            'Enterprise_CustomerSegment_Model_Resource_Segment_Collection', array(), array(), '', false
        );

        $this->_model = new Enterprise_BannerCustomerSegment_Model_Observer(
            $this->_resource, $this->_segmentCustomer, $this->_segmentHelper, $this->_segmentCollection
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_resource = null;
        $this->_segmentCustomer = null;
        $this->_segmentHelper = null;
        $this->_segmentCollection = null;
    }

    public function testLoadCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $adapter = $this->getMockForAbstractClass(
            'Zend_Db_Adapter_Abstract', array(), '', false, true, true, array('fetchAll')
        );
        $adapter
            ->expects($this->once())
            ->method('fetchAll')
            ->with($this->logicalAnd(
                $this->isInstanceOf('Zend_Db_Select'),
                $this->equalTo(
                    'SELECT "enterprise_banner_customersegment".*'
                        . ' FROM "enterprise_banner_customersegment"'
                        . ' WHERE (banner_id = 42)'
                )
            ))
            ->will($this->returnValue(array(array('segment_id' => 123), array('segment_id' => 456))))
        ;
        $this->_resource
            ->expects($this->any())->method('getConnection')->with('read')->will($this->returnValue($adapter));

        $banner = new Varien_Object(array('id' => 42));
        $this->_model->loadCustomerSegmentRelations(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('banner' => $banner)),
        )));
        $this->assertEquals(array(123, 456), $banner->getData('customer_segment_ids'));
    }

    public function testLoadCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_resource->expects($this->never())->method('getConnection');

        $banner = new Varien_Object(array('id' => 42));
        $this->_model->loadCustomerSegmentRelations(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('banner' => $banner)),
        )));
    }

    public function testSaveCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $expectedInsertRows = array(
            array('banner_id' => 42, 'segment_id' => 123),
            array('banner_id' => 42, 'segment_id' => 456),
        );

        $adapter = $this->getMockForAbstractClass(
            'Varien_Db_Adapter_Interface', array(), '', false, true, true, array('delete', 'insertMultiple')
        );
        $adapter
            ->expects($this->at(0))
            ->method('delete')
            ->with('enterprise_banner_customersegment', array('banner_id = ?' => 42))
        ;
        $adapter
            ->expects($this->at(1))
            ->method('insertMultiple')
            ->with('enterprise_banner_customersegment', $expectedInsertRows)
        ;
        $this->_resource
            ->expects($this->any())->method('getConnection')->with('write')->will($this->returnValue($adapter));

        $banner = new Varien_Object(array('id' => 42, 'customer_segment_ids' => array(123, 456)));
        $this->_model->saveCustomerSegmentRelations(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('banner' => $banner)),
        )));
    }

    public function testSaveCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_resource->expects($this->never())->method('getConnection');

        $banner = new Varien_Object(array('id' => 42, 'customer_segment_ids' => array(123, 456)));
        $this->_model->saveCustomerSegmentRelations(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('banner' => $banner)),
        )));
    }

    public function testAddFieldsToBannerForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $form = new Varien_Data_Form();
        $model = new Varien_Object();
        $block = $this->getMock('Mage_Backend_Block_Widget_Form_Element_Dependence', array(), array(), '', false);

        $this->_segmentHelper->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $block);

        $this->_model->addFieldsToBannerForm(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('form' => $form, 'model' => $model, 'after_form_block' => $block)),
        )));
    }

    public function testAddFieldsToBannerFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $form = new Varien_Data_Form();
        $model = new Varien_Object();
        $block = $this->getMock('Mage_Backend_Block_Widget_Form_Element_Dependence', array(), array(), '', false);

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToBannerForm(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('form' => $form, 'model' => $model, 'after_form_block' => $block)),
        )));
    }

    protected function _setFixtureSegmentIds(array $segmentIds)
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $this->_segmentCustomer
            ->expects($this->once())->method('getCurrentCustomerSegmentIds')->will($this->returnValue($segmentIds));
    }

    public function testAddCustomerSegmentFilterToCollection()
    {
        $this->_setFixtureSegmentIds(array(123, 456));

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));
        $select->from('enterprise_banner');

        $this->_model->addCustomerSegmentFilterToCollection(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('collection' => new Varien_Object(array('select' => $select)))),
        )));

        $this->assertEquals(
            'SELECT "enterprise_banner".* FROM "enterprise_banner"' . "\n"
                . ' LEFT JOIN "enterprise_banner_customersegment" AS "banner_segment"'
                . ' ON banner_segment.banner_id = main_table.banner_id'
                . ' WHERE (banner_segment.segment_id IS NULL OR banner_segment.segment_id IN (123, 456))',
            (string)$select
        );
    }

    public function testAddCustomerSegmentFilterToCollectionNoSegments()
    {
        $this->_setFixtureSegmentIds(array());

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));
        $select->from('enterprise_banner');

        $this->_model->addCustomerSegmentFilterToCollection(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('collection' => new Varien_Object(array('select' => $select)))),
        )));

        $this->assertEquals(
            'SELECT "enterprise_banner".* FROM "enterprise_banner"' . "\n"
                . ' LEFT JOIN "enterprise_banner_customersegment" AS "banner_segment"'
                . ' ON banner_segment.banner_id = main_table.banner_id'
                . ' WHERE (banner_segment.segment_id IS NULL)',
            (string)$select
        );
    }

    public function testAddCustomerSegmentFilterToCollectionDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');

        $this->_model->addCustomerSegmentFilterToCollection(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('collection' => new Varien_Object(array('select' => $select)))),
        )));

        $this->assertEmpty((string)$select);
    }

    public function testAddCustomerSegmentFilterToSelect()
    {
        $this->_setFixtureSegmentIds(array(123, 456));

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));
        $select->from('enterprise_banner');

        $this->_model->addCustomerSegmentFilterToSelect(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('select' => $select)),
        )));

        $this->assertEquals(
            'SELECT "enterprise_banner".* FROM "enterprise_banner"' . "\n"
                . ' LEFT JOIN "enterprise_banner_customersegment" AS "banner_segment"'
                . ' ON banner_segment.banner_id = main_table.banner_id'
                . ' WHERE (banner_segment.segment_id IS NULL OR banner_segment.segment_id IN (123, 456))',
            (string)$select
        );
    }

    public function testAddCustomerSegmentFilterToSelectNoSegments()
    {
        $this->_setFixtureSegmentIds(array());

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));
        $select->from('enterprise_banner');

        $this->_model->addCustomerSegmentFilterToSelect(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('select' => $select)),
        )));

        $this->assertEquals(
            'SELECT "enterprise_banner".* FROM "enterprise_banner"' . "\n"
                . ' LEFT JOIN "enterprise_banner_customersegment" AS "banner_segment"'
                . ' ON banner_segment.banner_id = main_table.banner_id'
                . ' WHERE (banner_segment.segment_id IS NULL)',
            (string)$select
        );
    }

    public function testAddCustomerSegmentFilterToSelectDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $select = new Zend_Db_Select($this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');

        $this->_model->addCustomerSegmentFilterToSelect(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('select' => $select)),
        )));

        $this->assertEmpty((string)$select);
    }
}
