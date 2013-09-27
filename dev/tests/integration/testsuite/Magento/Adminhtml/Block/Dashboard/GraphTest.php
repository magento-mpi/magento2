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
class Magento_Adminhtml_Block_Dashboard_GraphTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Adminhtml_Block_Dashboard_Graph
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Adminhtml_Block_Dashboard_Graph');
        $this->_block->setDataHelperName('Magento_Adminhtml_Helper_Dashboard_Order');
    }

    public function testGetChartUrl()
    {
        $this->assertStringStartsWith('http://chart.apis.google.com/chart', $this->_block->getChartUrl());
    }
}
