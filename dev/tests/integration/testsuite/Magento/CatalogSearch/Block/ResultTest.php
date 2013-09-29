<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Block;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    public function testSetListOrders()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $layout->addBlock('Magento\Core\Block\Text', 'head'); // The tested block is using head block
        /** @var $block \Magento\CatalogSearch\Block\Result */
        $block = $layout->addBlock('Magento\CatalogSearch\Block\Result', 'block');
        $childBlock = $layout->addBlock('Magento\Core\Block\Text', 'search_result_list', 'block');

        $this->assertSame($childBlock, $block->getListBlock());
    }
}
