<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Checkout_Block_Cart
 */
class Mage_Checkout_Block_CartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param Mage_Core_Block_Text|null $child
     * @param array $expected
     * @dataProvider getMethodsDataProvider
     */
    public function testGetMethods($child, $expected)
    {
        if ($child) {
            $block = $child->getLayout()
                ->createBlock('Mage_Checkout_Block_Cart')
                ->setChild('child', $child);
        } else {
            $layout = new Mage_Core_Model_Layout;
            $block = $layout->createBlock('Mage_Checkout_Block_Cart');
        }
        $methods = $block->getMethods('child');
        $this->assertEquals($expected, $methods);
    }

    public function getMethodsDataProvider()
    {
        $layout1 = new Mage_Core_Model_Layout;
        $child = $layout1->createBlock('Mage_Core_Block_Text')
            ->setChild('child1', $layout1->createBlock('Mage_Core_Block_Text', 'method1'))
            ->setChild('child2', $layout1->createBlock('Mage_Core_Block_Text', 'method2'));

        $layout2 = new Mage_Core_Model_Layout;
        $childEmpty = $layout2->createBlock('Mage_Core_Block_Text');

        return array(
            'with child blocks' => array($child, array('method1', 'method2')),
            'empty child' => array($childEmpty, array()),
            'no child' => array(null, array()),
        );
    }
}
