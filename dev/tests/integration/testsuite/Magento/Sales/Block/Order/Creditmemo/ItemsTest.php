<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Block_Order_Creditmemo_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Sales_Block_Order_Creditmemo_Items
     */
    protected $_block;

    /**
     * @var Magento_Sales_Model_Order_Creditmemo
     */
    protected $_creditmemo;

    protected function setUp()
    {
        $this->_layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Magento_Sales_Block_Order_Creditmemo_Items', 'block');
        $this->_creditmemo = Mage::getModel('Magento_Sales_Model_Order_Creditmemo');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetTotalsHtml()
    {
        $childBlock = $this->_layout->addBlock('Magento_Core_Block_Text', 'creditmemo_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getCreditmemo());
        $this->assertNotEquals($expectedHtml, $this->_block->getTotalsHtml($this->_creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getTotalsHtml($this->_creditmemo);
        $this->assertSame($this->_creditmemo, $childBlock->getCreditmemo());
        $this->assertEquals($expectedHtml, $actualHtml);
    }

    public function testGetCommentsHtml()
    {
        $childBlock = $this->_layout->addBlock('Magento_Core_Block_Text', 'creditmemo_comments', 'block');

        $expectedHtml = '<b>Any html</b>';
        $this->assertEmpty($childBlock->getEntity());
        $this->assertEmpty($childBlock->getTitle());
        $this->assertNotEquals($expectedHtml, $this->_block->getCommentsHtml($this->_creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $this->_block->getCommentsHtml($this->_creditmemo);
        $this->assertSame($this->_creditmemo, $childBlock->getEntity());
        $this->assertNotEmpty($childBlock->getTitle());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
