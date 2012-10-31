<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppIsolation enabled
 */
class Enterprise_Reward_Block_Adminhtml_Reward_Rate_GridTest extends PHPUnit_Framework_TestCase
{
    /** @var Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid */
    protected $_block;

    public function setUp()
    {
        $layout = new Mage_Core_Model_Layout();

        $this->_block = $layout->createBlock(
            'Enterprise_Reward_Block_Adminhtml_Reward_Rate_Grid'
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
        $websiteElement = $this->_block->getColumn('website_id');
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
        $websiteElement = $this->_block->getColumn('website_id');
        $this->assertNotNull($websiteElement);
        $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid_Column', $websiteElement);
    }
}
