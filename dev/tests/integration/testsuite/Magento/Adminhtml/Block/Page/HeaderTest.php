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
 * Test \Magento\Adminhtml\Block\Page\Header
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Page_HeaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Page\Header
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Page\Header');
    }

    public function testGetHomeLink()
    {
        $expected = Mage::helper('Magento\Backend\Helper\Data')->getHomePageUrl();
        $this->assertEquals($expected, $this->_block->getHomeLink());
    }
}
