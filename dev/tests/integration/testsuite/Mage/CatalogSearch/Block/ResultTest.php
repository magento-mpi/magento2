<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_CatalogSearch
 */
class Mage_CatalogSearch_Block_ResultTest extends PHPUnit_Framework_TestCase
{
    public function testSetListOrders()
    {
        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock('Mage_Core_Block_Text', 'head'); // The tested block is using head block
        $block = $layout->addBlock('Mage_CatalogSearch_Block_Result', 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'search_result_list', 'block');

        $this->assertEquals($childBlock, $block->getListBlock());
    }
}
