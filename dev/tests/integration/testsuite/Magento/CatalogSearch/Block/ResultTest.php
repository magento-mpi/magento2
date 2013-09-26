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

class Magento_CatalogSearch_Block_ResultTest extends PHPUnit_Framework_TestCase
{
    public function testSetListOrders()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        $layout->addBlock('Magento_Core_Block_Text', 'head'); // The tested block is using head block
        /** @var $block Magento_CatalogSearch_Block_Result */
        $block = $layout->addBlock('Magento_CatalogSearch_Block_Result', 'block');
        $childBlock = $layout->addBlock('Magento_Core_Block_Text', 'search_result_list', 'block');

        $this->assertSame($childBlock, $block->getListBlock());
    }
}
