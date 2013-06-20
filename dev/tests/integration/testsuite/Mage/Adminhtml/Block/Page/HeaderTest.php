<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Mage_Adminhtml_Block_Page_Header
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Page_HeaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Adminhtml_Block_Page_Header
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Page_Header');
    }

    public function testGetHomeLink()
    {
        $expected = Mage::helper('Mage_Backend_Helper_Data')->getHomePageUrl();
        $this->assertEquals($expected, $this->_block->getHomeLink());
    }
}
