<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_GridTest extends PHPUnit_Framework_TestCase
{
    /** @var Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid */
    protected $_block;

    public function setUp()
    {
        parent::setUp();
        $layout = Mage::getModel('Mage_Core_Model_Layout');

        $this->_block = $layout->createBlock(
            'Enterprise_GiftCardAccount_Block_Adminhtml_Giftcardaccount_Grid',
            'giftcardaccount.grid'
        );
    }

    /**
     * Test Prepare Columns for Single Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testPrepareColumnsSingleStore()
    {
        $this->_block->toHtml();
        $websiteElement = $this->_block->getColumn('website');
        $this->assertFalse($websiteElement);
    }

    /**
     * Test Prepare Columns for Multiple Store mode
     *
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testPrepareColumnsMultipleStore()
    {
        $this->_block->toHtml();
        $websiteElement = $this->_block->getColumn('website');
        $this->assertNotNull($websiteElement);
        $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid_Column', $websiteElement);
    }
}
