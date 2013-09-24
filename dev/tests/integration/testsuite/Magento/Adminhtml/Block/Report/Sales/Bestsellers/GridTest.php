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

namespace Magento\Adminhtml\Block\Report\Sales\Bestsellers;

/**
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Report\Sales\Bestsellers\Grid
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Report\Sales\Bestsellers\Grid');
    }

    public function testGetResourceCollectionName()
    {
        $collectionName = $this->_block->getResourceCollectionName();
        $this->assertTrue(class_exists($collectionName));
    }
}
