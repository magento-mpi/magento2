<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_BannerCustomerSegment_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BannerCustomerSegment\Model\Observer
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerSegmentLink;

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

    /**
     * @var Zend_Db_Select
     */
    private $_select;

    protected function setUp()
    {
        $this->_bannerSegmentLink = $this->getMock(
            'Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink',
            array('loadBannerSegments', 'saveBannerSegments', 'addBannerSegmentFilter'),
            array(), '', false
        );
        $this->_segmentCustomer = $this->getMock(
            'Magento\CustomerSegment\Model\Customer', array('getCurrentCustomerSegmentIds'), array(), '', false
        );
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data', array('isEnabled', 'addSegmentFieldsToForm'), array(), '', false
        );
        $this->_segmentCollection = $this->getMock(
            'Magento\CustomerSegment\Model\Resource\Segment\Collection', array(), array(), '', false
        );
        $this->_model = new \Magento\BannerCustomerSegment\Model\Observer(
            $this->_segmentCustomer, $this->_segmentHelper, $this->_segmentCollection, $this->_bannerSegmentLink
        );
        $this->_select = new Zend_Db_Select(
            $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false)
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_bannerSegmentLink = null;
        $this->_segmentCustomer = null;
        $this->_segmentHelper = null;
        $this->_segmentCollection = null;
        $this->_select = null;
    }

    public function testLoadCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $banner = new \Magento\Object(array('id' => 42));
        $segmentIds = array(123, 456);

        $this->_bannerSegmentLink
            ->expects($this->once())
            ->method('loadBannerSegments')
            ->with($banner->getId())
            ->will($this->returnValue($segmentIds))
        ;

        $this->_model->loadCustomerSegmentRelations(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('banner' => $banner)),
        )));
        $this->assertEquals($segmentIds, $banner->getData('customer_segment_ids'));
    }

    public function testLoadCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $banner = new \Magento\Object(array('id' => 42));

        $this->_bannerSegmentLink->expects($this->never())->method('loadBannerSegments');

        $this->_model->loadCustomerSegmentRelations(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('banner' => $banner)),
        )));
    }

    public function testSaveCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $segmentIds = array(123, 456);
        $banner = new \Magento\Object(array('id' => 42, 'customer_segment_ids' => $segmentIds));

        $this->_bannerSegmentLink
            ->expects($this->once())
            ->method('saveBannerSegments')
            ->with($banner->getId(), $segmentIds)
        ;

        $this->_model->saveCustomerSegmentRelations(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('banner' => $banner)),
        )));
    }

    /**
     * @expectedException UnexpectedValueException
     * @expectedExceptionMessage Customer segments associated with a banner are expected to be defined as an array
     */
    public function testSaveCustomerSegmentRelationsException()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $banner = new \Magento\Object(array('id' => 42, 'customer_segment_ids' => 'invalid'));

        $this->_bannerSegmentLink->expects($this->never())->method('saveBannerSegments');

        $this->_model->saveCustomerSegmentRelations(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('banner' => $banner)),
        )));
    }

    public function testSaveCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $banner = new \Magento\Object(array('id' => 42, 'customer_segment_ids' => array(123, 456)));

        $this->_bannerSegmentLink->expects($this->never())->method('saveBannerSegments');

        $this->_model->saveCustomerSegmentRelations(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('banner' => $banner)),
        )));
    }

    public function testAddFieldsToBannerForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $session = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
        $form = new \Magento\Data\Form($session, $factory, $collectionFactory);
        $model = new \Magento\Object();
        $block = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', array(), array(), '', false);

        $this->_segmentHelper->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $block);

        $this->_model->addFieldsToBannerForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'after_form_block' => $block)),
        )));
    }

    public function testAddFieldsToBannerFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $factory = $this->getMock('Magento\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactory = $this->getMock('Magento\Data\Form\Element\CollectionFactory', array('create'),
            array(), '', false);
        $session = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
        $form = new \Magento\Data\Form($session, $factory, $collectionFactory);
        $model = new \Magento\Object();
        $block = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', array(), array(), '', false);

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToBannerForm(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('form' => $form, 'model' => $model, 'after_form_block' => $block)),
        )));
    }

    protected function _setFixtureSegmentIds(array $segmentIds)
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $this->_segmentCustomer
            ->expects($this->once())->method('getCurrentCustomerSegmentIds')->will($this->returnValue($segmentIds));
    }

    /**
     * @dataProvider addCustomerSegmentFilterDataProvider
     * @param array $segmentIds
     */
    public function testAddCustomerSegmentFilterToCollection(array $segmentIds)
    {
        $this->_setFixtureSegmentIds($segmentIds);

        $this->_bannerSegmentLink
            ->expects($this->once())->method('addBannerSegmentFilter')->with($this->_select, $segmentIds);

        $this->_model->addCustomerSegmentFilterToCollection(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array(
                'collection' => new \Magento\Object(array('select' => $this->_select)))
            ),
        )));
    }

    public function addCustomerSegmentFilterDataProvider()
    {
        return array(
            'segments'      => array(array(123, 456)),
            'no segments'   => array(array()),
        );
    }

    public function testAddCustomerSegmentFilterToCollectionDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');
        $this->_bannerSegmentLink->expects($this->never())->method('addBannerSegmentFilter');

        $this->_model->addCustomerSegmentFilterToCollection(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array(
                'collection' => new \Magento\Object(array('select' => $this->_select))
            )),
        )));
    }

    /**
     * @dataProvider addCustomerSegmentFilterDataProvider
     * @param array $segmentIds
     */
    public function testAddCustomerSegmentFilterToSelect(array $segmentIds)
    {
        $this->_setFixtureSegmentIds($segmentIds);

        $this->_bannerSegmentLink
            ->expects($this->once())->method('addBannerSegmentFilter')->with($this->_select, $segmentIds);

        $this->_model->addCustomerSegmentFilterToSelect(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('select' => $this->_select)),
        )));
    }

    public function testAddCustomerSegmentFilterToSelectDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');
        $this->_bannerSegmentLink->expects($this->never())->method('addBannerSegmentFilter');

        $this->_model->addCustomerSegmentFilterToCollection(new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array('select' => $this->_select)),
        )));
    }
}
