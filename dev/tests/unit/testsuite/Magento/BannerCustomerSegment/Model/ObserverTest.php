<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\BannerCustomerSegment\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BannerCustomerSegment\Model\Observer
     */
    private $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_bannerSegmentLink;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCustomer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_segmentCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $_formKeyMock;

    /**
     * @var \Zend_Db_Select
     */
    private $_select;

    protected function setUp()
    {
        $this->_bannerSegmentLink = $this->getMock(
            'Magento\BannerCustomerSegment\Model\Resource\BannerSegmentLink',
            ['loadBannerSegments', 'saveBannerSegments', 'addBannerSegmentFilter', '__wakeup'],
            [],
            '',
            false
        );
        $this->_segmentCustomer = $this->getMock(
            'Magento\CustomerSegment\Model\Customer',
            ['getCurrentCustomerSegmentIds', '__wakeup'],
            [],
            '',
            false
        );
        $this->_segmentHelper = $this->getMock(
            'Magento\CustomerSegment\Helper\Data',
            ['isEnabled', 'addSegmentFieldsToForm'],
            [],
            '',
            false
        );
        $this->_segmentCollection = $this->getMock(
            'Magento\CustomerSegment\Model\Resource\Segment\Collection',
            [],
            [],
            '',
            false
        );
        $this->_model = new \Magento\BannerCustomerSegment\Model\Observer(
            $this->_segmentCustomer,
            $this->_segmentHelper,
            $this->_segmentCollection,
            $this->_bannerSegmentLink
        );
        $this->_select = new \Zend_Db_Select(
            $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', [], '', false)
        );
        $this->_formKeyMock = $this->getMock('Magento\Framework\Data\Form\FormKey', [], [], '', false);
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

        $banner = new \Magento\Framework\Object(['id' => 42]);
        $segmentIds = [123, 456];

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'loadBannerSegments'
        )->with(
            $banner->getId()
        )->will(
            $this->returnValue($segmentIds)
        );

        $this->_model->loadCustomerSegmentRelations(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(['banner' => $banner]),
                ]
            )
        );
        $this->assertEquals($segmentIds, $banner->getData('customer_segment_ids'));
    }

    public function testLoadCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $banner = new \Magento\Framework\Object(['id' => 42]);

        $this->_bannerSegmentLink->expects($this->never())->method('loadBannerSegments');

        $this->_model->loadCustomerSegmentRelations(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(['banner' => $banner]),
                ]
            )
        );
    }

    public function testSaveCustomerSegmentRelations()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $segmentIds = [123, 456];
        $banner = new \Magento\Framework\Object(['id' => 42, 'customer_segment_ids' => $segmentIds]);

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'saveBannerSegments'
        )->with(
            $banner->getId(),
            $segmentIds
        );

        $this->_model->saveCustomerSegmentRelations(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(['banner' => $banner]),
                ]
            )
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Customer segments associated with a banner are expected to be defined as an array
     */
    public function testSaveCustomerSegmentRelationsException()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $banner = new \Magento\Framework\Object(['id' => 42, 'customer_segment_ids' => 'invalid']);

        $this->_bannerSegmentLink->expects($this->never())->method('saveBannerSegments');

        $this->_model->saveCustomerSegmentRelations(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(['banner' => $banner]),
                ]
            )
        );
    }

    public function testSaveCustomerSegmentRelationsDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $banner = new \Magento\Framework\Object(['id' => 42, 'customer_segment_ids' => [123, 456]]);

        $this->_bannerSegmentLink->expects($this->never())->method('saveBannerSegments');

        $this->_model->saveCustomerSegmentRelations(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(['banner' => $banner]),
                ]
            )
        );
    }

    public function testAddFieldsToBannerForm()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $factory = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $form = new \Magento\Framework\Data\Form($factory, $collectionFactory, $this->_formKeyMock);
        $model = new \Magento\Framework\Object();
        $block = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', [], [], '', false);

        $this->_segmentHelper->expects($this->once())->method('addSegmentFieldsToForm')->with($form, $model, $block);

        $this->_model->addFieldsToBannerForm(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                        ['form' => $form, 'model' => $model, 'after_form_block' => $block]
                    ),
                ]
            )
        );
    }

    public function testAddFieldsToBannerFormDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $factory = $this->getMock('Magento\Framework\Data\Form\Element\Factory', [], [], '', false);
        $collectionFactory = $this->getMock(
            'Magento\Framework\Data\Form\Element\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $form = new \Magento\Framework\Data\Form($factory, $collectionFactory, $this->_formKeyMock);
        $model = new \Magento\Framework\Object();
        $block = $this->getMock('Magento\Backend\Block\Widget\Form\Element\Dependence', [], [], '', false);

        $this->_segmentHelper->expects($this->never())->method('addSegmentFieldsToForm');

        $this->_model->addFieldsToBannerForm(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                        ['form' => $form, 'model' => $model, 'after_form_block' => $block]
                    ),
                ]
            )
        );
    }

    protected function _setFixtureSegmentIds(array $segmentIds)
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(true));

        $this->_segmentCustomer->expects(
            $this->once()
        )->method(
            'getCurrentCustomerSegmentIds'
        )->will(
            $this->returnValue($segmentIds)
        );
    }

    /**
     * @dataProvider addCustomerSegmentFilterDataProvider
     * @param array $segmentIds
     */
    public function testAddCustomerSegmentFilterToCollection(array $segmentIds)
    {
        $this->_setFixtureSegmentIds($segmentIds);

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'addBannerSegmentFilter'
        )->with(
            $this->_select,
            $segmentIds
        );

        $this->_model->addCustomerSegmentFilterToCollection(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                        ['collection' => new \Magento\Framework\Object(['select' => $this->_select])]
                    ),
                ]
            )
        );
    }

    public function addCustomerSegmentFilterDataProvider()
    {
        return ['segments' => [[123, 456]], 'no segments' => [[]]];
    }

    public function testAddCustomerSegmentFilterToCollectionDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');
        $this->_bannerSegmentLink->expects($this->never())->method('addBannerSegmentFilter');

        $this->_model->addCustomerSegmentFilterToCollection(
            new \Magento\Framework\Event\Observer(
                [
                    'event' => new \Magento\Framework\Object(
                        ['collection' => new \Magento\Framework\Object(['select' => $this->_select])]
                    ),
                ]
            )
        );
    }

    /**
     * @dataProvider addCustomerSegmentFilterDataProvider
     * @param array $segmentIds
     */
    public function testAddCustomerSegmentFilterToSelect(array $segmentIds)
    {
        $this->_setFixtureSegmentIds($segmentIds);

        $this->_bannerSegmentLink->expects(
            $this->once()
        )->method(
            'addBannerSegmentFilter'
        )->with(
            $this->_select,
            $segmentIds
        );

        $this->_model->addCustomerSegmentFilterToSelect(
            new \Magento\Framework\Event\Observer(
                ['event' => new \Magento\Framework\Object(['select' => $this->_select])]
            )
        );
    }

    public function testAddCustomerSegmentFilterToSelectDisabled()
    {
        $this->_segmentHelper->expects($this->any())->method('isEnabled')->will($this->returnValue(false));

        $this->_segmentCustomer->expects($this->never())->method('getCurrentCustomerSegmentIds');
        $this->_bannerSegmentLink->expects($this->never())->method('addBannerSegmentFilter');

        $this->_model->addCustomerSegmentFilterToCollection(
            new \Magento\Framework\Event\Observer(
                ['event' => new \Magento\Framework\Object(['select' => $this->_select])]
            )
        );
    }
}
