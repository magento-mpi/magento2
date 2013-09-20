<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

class SearchTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasIndex()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML);

        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getModel(
            'Magento\Core\Model\Layout',
            array('area' => \Magento\Core\Model\App\Area::AREA_ADMINHTML)
        );
        $block = $layout->createBlock(
            'Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search',
            'block2');

        $indexValue = 'magento_index_set_to_test';
        $block->setIndex($indexValue);

        $html = $block->toHtml();
        $this->assertContains($indexValue, $html);
    }
}
