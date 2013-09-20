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
 * Test class for \Magento\Adminhtml\Block\Poll\Grid
 * @magentoAppArea adminhtml
 */
namespace Magento\Adminhtml\Block\Poll;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testPrepareColumns()
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\Adminhtml\Block\Poll\Grid');

        $prepareColumnsMethod = new \ReflectionMethod(
            'Magento\Adminhtml\Block\Poll\Grid', '_prepareColumns');
        $prepareColumnsMethod->setAccessible(true);
        $prepareColumnsMethod->invoke($block);

        foreach (array('date_posted', 'date_closed') as $id) {
            $column = $block->getColumn($id);
            $this->assertNotNull($column);
            $this->assertNotEmpty($column->getDateFormat());
        }
    }
}
