<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Payment\Block\Info\ContainerAbstract
 */
namespace Magento\Payment\Block\Info;

class ContainerAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testSetInfoTemplate()
    {
        $block = $this->getMock('Magento\Payment\Block\Info\ContainerAbstract',
            array('getChildBlock', 'getPaymentInfo'), array(), '', false);
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $paymentInfo = $objectManagerHelper->getObject('Magento\Payment\Model\Info');
        $adapterFactoryMock = $this->getMock('Magento\Core\Model\Log\AdapterFactory', array('create'),
            array(), '', false);
        $methodInstance = $objectManagerHelper->getObject('Magento\Payment\Model\Method\Checkmo', array(
            'logAdapterFactory' => $adapterFactoryMock,
        ));
        $paymentInfo->setMethodInstance($methodInstance);
        $block->expects($this->atLeastOnce())
            ->method('getPaymentInfo')
            ->will($this->returnValue($paymentInfo));

        $childBlock = $objectManagerHelper->getObject('Magento\Core\Block\Template');
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('payment.info.checkmo')
            ->will($this->returnValue($childBlock));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlock->getTemplate());
        $block->setInfoTemplate('checkmo', $template);
        $this->assertEquals($template, $childBlock->getTemplate());
    }
}
