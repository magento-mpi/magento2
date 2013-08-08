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
 * Test class for Mage_Checkout_Block_Onepage_Payment_Methods
 */
class Mage_Checkout_Block_Onepage_Payment_MethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Checkout_Block_Onepage_Payment_Methods
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::getModel('Magento_Core_Model_Layout')
            ->createBlock('Mage_Checkout_Block_Onepage_Payment_Methods');
    }

    public function testGetMethodTitleAndMethodLabelAfterHtml()
    {
        $expectedTitle = 'Free Method';
        $expectedLabel = 'Label After Html';
        $method = Mage::getModel('Mage_Payment_Model_Method_Free');

        $block = $this->_block->getLayout()->createBlock('Magento_Core_Block_Text')
            ->setMethodTitle($expectedTitle)
            ->setMethodLabelAfterHtml($expectedLabel);

        $this->assertEquals('No Payment Information Required', $this->_block->getMethodTitle($method));
        $this->_block->setChild('payment.method.free', $block);
        $actualTitle = $this->_block->getMethodTitle($method);
        $actualLabel = $this->_block->getMethodLabelAfterHtml($method);

        $this->assertEquals($expectedTitle, $actualTitle);
        $this->assertEquals($expectedLabel, $actualLabel);
    }
}
