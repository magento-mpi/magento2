<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Poll_Grid
 * @magentoAppArea adminhtml
 */
class Mage_Adminhtml_Block_Poll_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Mage_Core_Model_Layout');
        $block = $layout->addBlock('Mage_Adminhtml_Block_Poll_Grid');

        $prepareColumnsMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Poll_Grid', '_prepareColumns');
        $prepareColumnsMethod->setAccessible(true);
        $prepareColumnsMethod->invoke($block);

        foreach (array('date_posted', 'date_closed') as $id) {
            $column = $block->getColumn($id);
            $this->assertNotNull($column);
            $this->assertNotEmpty($column->getDateFormat());
        }
    }
}
