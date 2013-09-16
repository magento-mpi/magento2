<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Checkout_Block_Onepage_Payment_Methods
 */
class Magento_Checkout_Block_Onepage_Payment_MethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Checkout_Block_Onepage_Payment_Methods
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::getSingleton('Magento_Core_Model_Layout')
            ->createBlock('Magento_Checkout_Block_Onepage_Payment_Methods');
    }

    public function testGetMethodTitleAndMethodLabelAfterHtml()
    {
        $expectedTitle = 'Free Method';
        $expectedLabel = 'Label After Html';
        $method = Mage::getModel('Magento_Payment_Model_Method_Free');

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
