<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Block\Adminhtml\Grid\Filter;

/**
 * @magentoAppArea adminhtml
 */
class IpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Logging\Block\Adminhtml\Grid\Filter\Ip
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock('Magento\Logging\Block\Adminhtml\Grid\Filter\Ip');
    }

    public function testGetCondition()
    {
        $condition = $this->_block->getCondition();
        $this->assertArrayHasKey('field_expr', $condition);
        $this->assertArrayHasKey('like', $condition);
    }

    public function testGetConditionWithLike()
    {
        $this->_block->setValue('127');
        $condition = $this->_block->getCondition();
        $this->assertContains('127', (string) $condition['like']);
        $this->assertNotEquals('127', (string) $condition['like']); // DB-depended placeholder symbols were added
    }
}
