<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringProfile\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Element\BlockFactory
     */
    protected $_blockFactory;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\RecurringProfile\Model\Observer
     */
    protected $_testModel;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fieldsBlock;

    /**
     * @var \Magento\RecurringProfile\Model\RecurringProfileFactory
     */
    protected $_profileFactory;

    /**
     * @var \Magento\Event
     */
    protected $_event;
    
    protected function setUp()
    {
        $this->_blockFactory = $this->getMock(
            'Magento\View\Element\BlockFactory', ['createBlock'], [], '', false
        );
        $this->_observer = $this->getMock('Magento\Event\Observer', [], [], '', false);
        $this->_fieldsBlock = $this->getMock('\Magento\RecurringProfile\Block\Fields', ['getFieldLabel'], [], '', false);
        $this->_profileFactory = $this->getMock(
            '\Magento\RecurringProfile\Model\RecurringProfileFactory', ['create'], [], '', false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        /** @var \Magento\RecurringProfile\Model\Observer $this->_testModel */
        $this->_testModel = $helper->getObject('Magento\RecurringProfile\Model\Observer', [
            'blockFactory' => $this->_blockFactory,
            'profileFactory' => $this->_profileFactory,
            'fields' => $this->_fieldsBlock
        ]);

        $this->_event = $this->getMock(
            'Magento\Event', ['getProductElement', 'getProduct', 'getResult', 'getBuyRequest'], [], '', false
        );

        $this->_observer->expects($this->any())->method('getEvent')->will($this->returnValue($this->_event));
    }

    public function testPrepareProductRecurringProfileOptions()
    {
        $profile = $this->getMock(
            'Magento\Object',
            [
                'setStory', 'importBuyRequest', 'importProduct', 'exportStartDatetime', 'exportScheduleInfo',
                'getFieldLabel'
            ],
            [],
            '',
            false
        );
        $profile->expects($this->once())->method('exportStartDatetime')->will($this->returnValue('date'));
        $profile->expects($this->any())->method('setStore')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('importBuyRequest')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('exportScheduleInfo')->will($this->returnValue([
            new \Magento\Object(['title' => 'Title', 'schedule' => 'schedule'])
        ]));

        $this->_fieldsBlock->expects($this->once())->method('getFieldLabel')->will($this->returnValue('Field Label'));

        $this->_profileFactory->expects($this->once())->method('create')->will($this->returnValue($profile));

        $product = $this->getMock('Magento\Object', ['isRecurring', 'addCustomOption'], [], '', false);
        $product->expects($this->once())->method('isRecurring')->will($this->returnValue(true));

        $infoOptions = [
            ['label' => 'Field Label', 'value' => 'date'],
            ['label' => 'Title', 'value' => 'schedule']
        ];

        $product->expects($this->at(2))->method('addCustomOption')->with(
            'additional_options',
            serialize($infoOptions)
        );

        $this->_event->expects($this->any())->method('getProduct')->will($this->returnValue($product));

        $this->_testModel->prepareProductRecurringProfileOptions($this->_observer);
    }

    public function testRenderRecurringProfileForm()
    {
        $blockMock = $this->getMock(
            'Magento\View\Element\BlockInterface',
            [
                'setNameInLayout', 'setParentElement', 'setProductEntity', 'toHtml', 'addFieldMap',
                'addFieldDependence', 'addConfigOptions'
            ]
        );
        $map = [
            ['\Magento\RecurringProfile\Block\Adminhtml\Profile\Edit\Form', [], $blockMock],
            ['Magento\Backend\Block\Widget\Form\Element\Dependence', [], $blockMock]
        ];
        $profileElement = $this->getMock('Magento\Data\Form\Element\AbstractElement', [], [], '', false);
        $this->_event->expects($this->once())->method('getProductElement')->will($this->returnValue($profileElement));
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->_event->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $this->_blockFactory->expects($this->any())->method('createBlock')->will($this->returnValueMap($map));
        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setParentElement')->with($profileElement);
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(['levels_up' => 2]);
        $result = new \StdClass();
        $this->_event->expects($this->once())->method('getResult')->will($this->returnValue($result));
        $this->_testModel->addFieldsToProductEditForm($this->_observer);
        $this->assertEquals('htmlhtml', $result->output);
    }
}
