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

    public function testPrepareProductRecurringProfileOptions()
    {
        $blockFactoryMock = $this->getMock(
            'Magento\View\Element\BlockFactory', ['createBlock'], [], '', false
        );
        $profile = $this->getMock(
            'Magento\Object',
            [
                'setLocale', 'setStory', 'importBuyRequest', 'importProduct', 'exportStartDatetime',
                'exportScheduleInfo', 'getFieldLabel'
            ],
            [],
            '',
            false
        );
        $profile->expects($this->once())->method('getFieldLabel')->will($this->returnValue('Field Label'));
        $profile->expects($this->once())->method('exportStartDatetime')->will($this->returnValue('date'));
        $profile->expects($this->once())->method('setLocale')->will($this->returnValue($profile));
        $profile->expects($this->any())->method('setStore')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('importBuyRequest')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('exportScheduleInfo')->will($this->returnValue([
            new \Magento\Object(['title' => 'Title', 'schedule' => 'schedule'])
        ]));
        $profileFactory = $this->getMock(
            '\Magento\RecurringProfile\Model\RecurringProfileFactory', ['create'], [], '', false
        );
        $profileFactory->expects($this->once())->method('create')->will($this->returnValue($profile));
        $observerMock = $this->getMock('Magento\Event\Observer', [], [], '', false);

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $model = $helper->getObject('Magento\RecurringProfile\Model\Observer', [
            'blockFactory' => $blockFactoryMock,
            'profileFactory' => $profileFactory
        ]);

        $event = $this->getMock(
            'Magento\Event', ['getBuyRequest', 'getProduct'], [], '', false
        );
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

        $event->expects($this->any())->method('getProduct')->will($this->returnValue($product));

        $observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($event));

        $model->prepareProductRecurringProfileOptions($observerMock);
    }

    public function testRender()
    {
        $blockFactoryMock = $this->getMock(
            'Magento\View\Element\BlockFactory', ['createBlock'], [], '', false
        );
        $observerMock = $this->getMock('Magento\Event\Observer', [], [], '', false);

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $model = $helper->getObject('Magento\RecurringProfile\Model\Observer', [
            'blockFactory' => $blockFactoryMock
        ]);
        $blockMock = $this->getMock(
            'Magento\View\Element\BlockInterface',
            [
                'setNameInLayout', 'setParentElement', 'setProductEntity', 'toHtml', 'addFieldMap',
                'addFieldDependence', 'addConfigOptions'
            ]
        );
        $map = [
            ['Magento\Sales\Block\Adminhtml\Recurring\Profile\Edit\Form', [], $blockMock],
            ['Magento\Backend\Block\Widget\Form\Element\Dependence', [], $blockMock]
        ];
        $event = $this->getMock(
            'Magento\Event', ['getProductElement', 'getProduct', 'getResult'], [], '', false
        );
        $observerMock->expects($this->any())->method('getEvent')->will($this->returnValue($event));
        $profileElement = $this->getMock('Magento\Data\Form\Element\AbstractElement', [], [], '', false);
        $event->expects($this->once())->method('getProductElement')->will($this->returnValue($profileElement));
        $product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $blockFactoryMock->expects($this->any())->method('createBlock')->will($this->returnValueMap($map));
        $blockMock->expects($this->any())->method('setNameInLayout');
        $blockMock->expects($this->once())->method('setParentElement')->with($profileElement);
        $blockMock->expects($this->once())->method('setProductEntity')->with($product);
        $blockMock->expects($this->exactly(2))->method('toHtml')->will($this->returnValue('html'));
        $blockMock->expects($this->once())->method('addConfigOptions')->with(['levels_up' => 2]);
        $result = new \StdClass();
        $event->expects($this->once())->method('getResult')->will($this->returnValue($result));
        $model->renderForm($observerMock);
        $this->assertEquals('htmlhtml', $result->output);
    }
}
