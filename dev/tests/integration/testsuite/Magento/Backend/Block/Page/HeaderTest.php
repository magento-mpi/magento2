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
namespace Magento\Backend\Block\Page;

/**
 * Test \Magento\Backend\Block\Page\Header
 * @magentoAppArea adminhtml
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Block\Page\Header
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Backend\Block\Page\Header'
        );
    }

    public function testGetHomeLink()
    {
        $expected = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Backend\Helper\Data'
        )->getHomePageUrl();
        $this->assertEquals($expected, $this->_block->getHomeLink());
    }
}
