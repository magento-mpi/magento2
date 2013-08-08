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
 * Test class for Magento_Adminhtml_Block_Poll_Grid
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Poll_GridTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        $layout = Mage::getObjectManager()->create('Magento_Core_Model_Layout');
        $block = $layout->addBlock('Magento_Adminhtml_Block_Poll_Grid');

        $prepareColumnsMethod = new ReflectionMethod(
            'Magento_Adminhtml_Block_Poll_Grid', '_prepareColumns');
        $prepareColumnsMethod->setAccessible(true);
        $prepareColumnsMethod->invoke($block);

        foreach (array('date_posted', 'date_closed') as $id) {
            $column = $block->getColumn($id);
            $this->assertNotNull($column);
            $this->assertNotEmpty($column->getDateFormat());
        }
    }
}
