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

namespace Magento\Adminhtml\Block\Page;

/**
 * Test \Magento\Adminhtml\Block\Page\Header
 * @magentoAppArea adminhtml
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Page\Header
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Page\Header');
    }

    public function testGetHomeLink()
    {
        $expected = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Backend\Helper\Data')
            ->getHomePageUrl();
        $this->assertEquals($expected, $this->_block->getHomeLink());
    }
}
