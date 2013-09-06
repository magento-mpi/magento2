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
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Report_Sales_Bestsellers_GridTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_Report_Sales_Bestsellers_Grid
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Report_Sales_Bestsellers_Grid');
    }

    public function testGetResourceCollectionName()
    {
        $collectionName = $this->_block->getResourceCollectionName();
        $this->assertTrue(class_exists($collectionName));
    }
}
