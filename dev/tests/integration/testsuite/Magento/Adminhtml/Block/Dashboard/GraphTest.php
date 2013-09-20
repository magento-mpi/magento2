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
namespace Magento\Adminhtml\Block\Dashboard;

class GraphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Dashboard\Graph
     */
    protected $_block;

    protected function setUp()
    {
        parent::setUp();
        $this->_block = \Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Dashboard\Graph');
        $this->_block->setDataHelperName('Magento\Adminhtml\Helper\Dashboard\Order');
    }

    public function testGetChartUrl()
    {
        $this->assertStringStartsWith('http://chart.apis.google.com/chart', $this->_block->getChartUrl());
    }
}
