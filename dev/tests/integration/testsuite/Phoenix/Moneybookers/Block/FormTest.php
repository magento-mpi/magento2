<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Phoenix_Moneybookers
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Phoenix_Moneybookers_Block_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Phoenix_Moneybookers_Block_Form
     */
    protected $_block;

    protected function setUp()
    {
        /** @var $blockFactory Mage_Core_Model_BlockFactory */
        $blockFactory = Mage::getObjectManager()->get('Mage_Core_Model_BlockFactory');
        $this->_block = $blockFactory->createBlock('Phoenix_Moneybookers_Block_Form');
    }

    protected function tearDown()
    {
        unset($this->_block);
    }

    public function testConstruct()
    {
        $this->assertStringEndsWith('form.phtml', $this->_block->getTemplate());
    }

    public function testGetPaymentImageSrc()
    {
        $this->assertStringEndsWith('moneybookers_acc.png', $this->_block->getPaymentImageSrc('moneybookers_acc'));
        $this->assertStringEndsWith('moneybookers_csi.gif', $this->_block->getPaymentImageSrc('moneybookers_csi'));
        $this->assertFalse($this->_block->getPaymentImageSrc('moneybookers_nonexisting'));
    }
}
