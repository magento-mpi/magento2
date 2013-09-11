<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Block_Product_View_AdditionalTest extends PHPUnit_Framework_TestCase
{
    public function testGetChildHtmlList()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = Mage::getModel('Magento\Core\Model\Layout');
        /** @var $block \Magento\Catalog\Block\Product\View\Additional */
        $block = $layout->createBlock('Magento\Catalog\Block\Product\View\Additional', 'block');

        /** @var $childFirst \Magento\Core\Block\Text */
        $childFirst = $layout->addBlock('\Magento\Core\Block\Text', 'child1', 'block');
        $htmlFirst = '<b>Any html of child1</b>';
        $childFirst->setText($htmlFirst);

        /** @var $childSecond \Magento\Core\Block\Text */
        $childSecond = $layout->addBlock('\Magento\Core\Block\Text', 'child2', 'block');
        $htmlSecond = '<b>Any html of child2</b>';
        $childSecond->setText($htmlSecond);

        $list = $block->getChildHtmlList();

        $this->assertInternalType('array', $list);
        $this->assertCount(2, $list);
        $this->assertContains($htmlFirst, $list);
        $this->assertContains($htmlSecond, $list);
    }
}
