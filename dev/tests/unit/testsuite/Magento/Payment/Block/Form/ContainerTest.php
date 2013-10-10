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
 * Test class for \Magento\Payment\Block\Form\AbstractContainer
 */
namespace Magento\Payment\Block\Form;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Payment\Block\Form\AbstractContainer::getChildBlock
     */
    public function testSetMethodFormTemplate()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $childBlockA = $objectManagerHelper->getObject('Magento\Core\Block\Template');
        $childBlockB = $objectManagerHelper->getObject('Magento\Core\Block\Template');

        $func = function ($blockName) use ($childBlockA, $childBlockB) {
            switch ($blockName) {
                case 'payment.method.a':
                    return $childBlockA;
                case 'payment.method.b':
                    return $childBlockB;
            }
            return null;
        };
        $block = $this->getMock('Magento\Payment\Block\Form\Container', array('getChildBlock'),
            array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->will($this->returnCallback($func));

        $template = 'any_template.phtml';
        $this->assertNotEquals($template, $childBlockA->getTemplate());
        $this->assertNotEquals($template, $childBlockB->getTemplate());

        $block->setMethodFormTemplate('a', $template);
        $this->assertEquals($template, $childBlockA->getTemplate()); // Template is set to the block
        $this->assertNotEquals($template, $childBlockB->getTemplate()); // Template is not propagated to other blocks
    }
}
